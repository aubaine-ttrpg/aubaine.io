<?php

declare(strict_types=1);

namespace App\Controller;

use App\Book\BookEditor;
use App\Book\BookRepository;
use App\Book\Dto\BookMeta;
use App\Book\Form\BookMetaType;
use App\Book\Model\Book;
use App\Book\Version;
use App\Page\PageTypeRegistry;
use App\Page\PageViewFactory;
use App\Pdf\BookFingerprint;
use App\Pdf\BookPdfLibrary;
use App\Pdf\BookRelease;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
            $book = $editor->create(
                $meta->title,
                $meta->subtitle,
                $meta->bookType,
                new Version($meta->versionMajor, $meta->versionMinor),
            );
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

    #[Route('/books/{id}/settings', name: 'app_book_settings', methods: ['GET', 'POST'])]
    public function settings(string $id, Request $request, BookRepository $books, BookEditor $editor): Response
    {
        $book = $books->find($id);
        $meta = BookMeta::fromBook($book);
        $form = $this->createForm(BookMetaType::class, $meta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editor->updateMeta(
                $book,
                $meta->title,
                $meta->subtitle,
                $meta->bookType,
                new Version($meta->versionMajor, $meta->versionMinor),
            );
            $this->addFlash('success', 'flash.book_saved');

            return $this->redirectToRoute('app_book_edit', ['id' => $id]);
        }

        return $this->render('book/settings.html.twig', ['book' => $book, 'form' => $form], $this->formResponse($form->isSubmitted()));
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
    public function print(string $id, Request $request, BookRepository $books, PageViewFactory $views, BookFingerprint $fingerprint): Response
    {
        $book = $books->find($id);
        $release = $this->resolveRelease($request, $book, $fingerprint);

        return $this->render('print/book.html.twig', [
            'book' => $book,
            'pages' => $views->forBook($book, $release),
        ]);
    }

    /**
     * Server-side PDF of the printable book. The version stamp (webpack + book
     * hashes) is computed first, then the PDF is served from the content-addressed
     * cache (`bookName_webpack_book.pdf`) or built by Gotenberg if absent.
     * `?download=1` forces a download; otherwise it opens inline.
     */
    #[Route('/books/{id}/pdf', name: 'app_book_pdf', methods: ['GET'])]
    public function pdf(
        string $id,
        Request $request,
        BookRepository $books,
        BookFingerprint $fingerprint,
        BookPdfLibrary $library,
    ): Response {
        $book = $books->find($id);
        $release = $fingerprint->release($book);
        $path = $library->render($book, $release);

        $disposition = $request->query->getBoolean('download')
            ? HeaderUtils::DISPOSITION_ATTACHMENT
            : HeaderUtils::DISPOSITION_INLINE;

        return $this->file($path, $release->fileName($book->id()), $disposition);
    }

    /**
     * The release for the print route: trust the hashes the PDF action passed
     * (so the printed cover matches the filename exactly), else recompute for a
     * direct browser visit. Query hashes are validated before being trusted.
     */
    private function resolveRelease(Request $request, Book $book, BookFingerprint $fingerprint): BookRelease
    {
        $wp = (string) $request->query->get('wp', '');
        $bk = (string) $request->query->get('bk', '');
        if (1 === preg_match('/^[0-9a-f]{8}$/', $wp) && 1 === preg_match('/^[0-9a-f]{8}$/', $bk)) {
            return new BookRelease($book->version(), $wp, $bk);
        }

        return $fingerprint->release($book);
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

    private function indexOfPage(Book $book, string $pageId): int
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
