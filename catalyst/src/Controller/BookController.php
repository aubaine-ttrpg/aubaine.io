<?php

declare(strict_types=1);

namespace App\Controller;

use App\Book\BookEditor;
use App\Book\BookRepository;
use App\Book\Dto\BookMeta;
use App\Book\Form\BookMetaType;
use App\Page\PageTypeRegistry;
use App\Page\PageViewFactory;
use App\Pdf\Exception\PdfRenderingException;
use Psr\Log\LoggerInterface;
use Sensiolabs\GotenbergBundle\Exception\ClientException;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * The book creator: list, create, edit (add/customize/reorder/remove pages),
 * print, download as PDF, delete. Actions stay thin, delegating to the editor,
 * repository, page-type registry, and PDF renderer.
 */
final class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $books): Response
    {
        return $this->render('book/index.html.twig', ['books' => $books->all()]);
    }

    #[Route('/books/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BookEditor $editor): Response
    {
        $meta = new BookMeta();
        $form = $this->createForm(BookMetaType::class, $meta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $editor->create($meta->title, $meta->subtitle);
            $this->addFlash('success', 'flash.book_created');

            return $this->redirectToRoute('app_book_edit', ['id' => $book->id()]);
        }

        return $this->render('book/new.html.twig', ['form' => $form], $this->formResponse($form->isSubmitted()));
    }

    #[Route('/books/{id}', name: 'app_book_edit', methods: ['GET'])]
    public function edit(string $id, BookRepository $books, PageTypeRegistry $registry): Response
    {
        $book = $books->find($id);

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'catalog' => $registry->byCategory(),
        ]);
    }

    #[Route('/books/{id}/pages', name: 'app_book_page_add', methods: ['POST'])]
    public function addPage(string $id, Request $request, BookRepository $books, BookEditor $editor): Response
    {
        $book = $books->find($id);
        $this->assertCsrf('add-page', $request);

        $type = (string) $request->request->get('type', '');
        $editor->addPage($book, $type);
        $this->addFlash('success', 'flash.page_added');

        return $this->redirectToRoute('app_book_edit', ['id' => $id]);
    }

    #[Route('/books/{id}/pages/{pageId}', name: 'app_book_page_edit', methods: ['GET', 'POST'])]
    public function editPage(
        string $id,
        string $pageId,
        Request $request,
        BookRepository $books,
        BookEditor $editor,
        PageTypeRegistry $registry,
    ): Response {
        $book = $books->find($id);
        $page = $book->findPage($pageId);
        $type = $registry->get($page->type());

        $form = $this->createForm($type->formType(), $page->data());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, mixed> $data */
            $data = $form->getData();
            $editor->updatePageData($book, $pageId, $data);
            $this->addFlash('success', 'flash.page_saved');

            return $this->redirectToRoute('app_book_edit', ['id' => $id]);
        }

        return $this->render('book/page_edit.html.twig', [
            'book' => $book,
            'page' => $page,
            'type' => $type,
            'form' => $form,
        ], $this->formResponse($form->isSubmitted()));
    }

    #[Route('/books/{id}/pages/{pageId}/move', name: 'app_book_page_move', methods: ['POST'])]
    public function movePage(string $id, string $pageId, Request $request, BookRepository $books, BookEditor $editor): Response
    {
        $book = $books->find($id);
        $this->assertCsrf('move-page', $request);

        $direction = (string) $request->request->get('direction', 'down');
        $index = $this->indexOfPage($book, $pageId);
        $editor->movePage($book, $pageId, 'up' === $direction ? $index - 1 : $index + 1);

        return $this->redirectToRoute('app_book_edit', ['id' => $id]);
    }

    #[Route('/books/{id}/pages/{pageId}/delete', name: 'app_book_page_delete', methods: ['POST'])]
    public function deletePage(string $id, string $pageId, Request $request, BookRepository $books, BookEditor $editor): Response
    {
        $book = $books->find($id);
        $this->assertCsrf('delete-page', $request);

        $editor->removePage($book, $pageId);
        $this->addFlash('success', 'flash.page_removed');

        return $this->redirectToRoute('app_book_edit', ['id' => $id]);
    }

    #[Route('/books/{id}/print', name: 'app_book_print', methods: ['GET'])]
    public function print(string $id, BookRepository $books, PageViewFactory $views): Response
    {
        $book = $books->find($id);

        return $this->render('print/book.html.twig', [
            'book' => $book,
            'pages' => $views->forBook($book),
        ]);
    }

    /**
     * Server-side PDF of the printable book, rendered by Gotenberg from the
     * print route. `?download=1` forces a download; otherwise it opens inline.
     */
    #[Route('/books/{id}/pdf', name: 'app_book_pdf', methods: ['GET'])]
    public function pdf(
        string $id,
        Request $request,
        BookRepository $books,
        GotenbergPdfInterface $gotenberg,
        LoggerInterface $logger,
    ): Response {
        $book = $books->find($id);

        $slug = (new AsciiSlugger())->slug($book->title())->lower()->toString();
        $filename = '' !== $slug ? $slug : 'book';
        $disposition = $request->query->getBoolean('download')
            ? HeaderUtils::DISPOSITION_ATTACHMENT
            : HeaderUtils::DISPOSITION_INLINE;

        try {
            return $gotenberg->url()
                ->route('app_book_print', ['id' => $book->id()])
                ->printBackground()      // cover art, paper textures, dark cover backgrounds
                ->preferCssPageSize()    // honour the CSS @page A4 + margin:0 (full bleed)
                ->fileName($filename, $disposition)
                ->generate()
                ->stream();
        } catch (ClientException $e) {
            $logger->error('book.pdf_render_failed', ['event' => 'book.pdf_render_failed', 'book_id' => $id]);

            throw PdfRenderingException::forBook($id, $e);
        }
    }

    #[Route('/books/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    public function delete(string $id, Request $request, BookRepository $books, BookEditor $editor): Response
    {
        $book = $books->find($id);
        $this->assertCsrf('delete-book', $request);

        $editor->delete($book);
        $this->addFlash('success', 'flash.book_deleted');

        return $this->redirectToRoute('app_book_index');
    }

    private function assertCsrf(string $id, Request $request): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }
    }

    private function indexOfPage(\App\Book\Model\Book $book, string $pageId): int
    {
        foreach ($book->pages() as $index => $page) {
            if ($page->id() === $pageId) {
                return $index;
            }
        }

        // findPage throws the proper 404 if the page is gone.
        $book->findPage($pageId);

        return 0;
    }

    private function formResponse(bool $submitted): Response
    {
        return new Response(status: $submitted ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);
    }
}
