<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Book\BookEditor;
use App\Book\BookRepository;
use App\Book\Exception\PageNotFoundException;
use App\Book\Model\Book;
use App\Book\Model\Page;
use App\Page\PageTypeInterface;
use App\Page\PageTypeRegistry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * The live book editor: page list, add-page catalog, a WYSIWYG preview of the
 * printable book, and an inline form to customize the selected page. Every
 * mutation delegates to {@see BookEditor} (the one place book changes happen);
 * this component only tracks which page is selected and bumps a preview nonce
 * so the Stimulus controller knows when to reload the preview iframe.
 *
 * With JavaScript off it still renders as a server-side page and every control
 * is a real form/link posting to the existing BookController routes.
 */
#[AsLiveComponent]
final class LiveBookEditor
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public string $bookId = '';

    #[LiveProp(writable: true)]
    public ?string $selectedPageId = null;

    /** Bumped by every mutating action; the only trigger for a preview reload. */
    #[LiveProp]
    public int $previewNonce = 0;

    /** Translation key announced in the aria-live status region after an action. */
    #[LiveProp(writable: true)]
    public string $status = '';

    public function __construct(
        private readonly BookRepository $books,
        private readonly BookEditor $editor,
        private readonly PageTypeRegistry $pageTypes,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    public function mount(string $bookId, ?string $selectedPageId = null): void
    {
        $this->bookId = $bookId;
        $this->selectedPageId = $this->resolveSelection($this->getBook(), $selectedPageId);
    }

    public function getBook(): Book
    {
        return $this->books->find($this->bookId);
    }

    /**
     * @return array<string, list<PageTypeInterface>>
     */
    public function getCatalog(): array
    {
        return $this->pageTypes->byCategory();
    }

    public function getSelectedPage(): ?Page
    {
        if (null === $this->selectedPageId) {
            return null;
        }

        try {
            return $this->getBook()->findPage($this->selectedPageId);
        } catch (PageNotFoundException) {
            return null;
        }
    }

    #[LiveAction]
    public function selectPage(#[LiveArg] string $pageId): void
    {
        $this->selectedPageId = $pageId;
        $this->status = '';
        $this->resetForm();
    }

    #[LiveAction]
    public function addPage(#[LiveArg] string $type): void
    {
        $page = $this->editor->addPage($this->getBook(), $type);
        $this->selectedPageId = $page->id();
        $this->status = 'editor.page_added';
        $this->resetForm();
        ++$this->previewNonce;
    }

    #[LiveAction]
    public function movePage(#[LiveArg] string $pageId, #[LiveArg] string $direction): void
    {
        $book = $this->getBook();
        $index = $book->indexOf($pageId);
        $this->editor->movePage($book, $pageId, 'up' === $direction ? $index - 1 : $index + 1);
        ++$this->previewNonce;
    }

    #[LiveAction]
    public function deletePage(#[LiveArg] string $pageId): void
    {
        $this->editor->removePage($this->getBook(), $pageId);
        $this->status = 'editor.page_removed';

        if ($this->selectedPageId === $pageId) {
            $pages = $this->getBook()->pages();
            $this->selectedPageId = isset($pages[0]) ? $pages[0]->id() : null;
            $this->resetForm();
        }

        ++$this->previewNonce;
    }

    #[LiveAction]
    public function save(): void
    {
        $pageId = $this->selectedPageId;
        if (null === $pageId) {
            return;
        }

        // Throws (422) on invalid input; the component re-renders with field errors.
        $this->submitForm();

        /** @var array<string, mixed> $data */
        $data = $this->getForm()->getData();
        $this->editor->updatePageData($this->getBook(), $pageId, $data);
        $this->status = 'editor.page_saved';
        ++$this->previewNonce;
    }

    /**
     * @return FormInterface<mixed>
     */
    protected function instantiateForm(): FormInterface
    {
        $page = $this->getSelectedPage();
        if (null === $page) {
            // No page selected (empty book): a throwaway form the template never
            // renders and never submits, so it carries no CSRF token.
            return $this->formFactory->createBuilder(options: ['csrf_protection' => false])->getForm();
        }

        return $this->formFactory->create($this->pageTypes->get($page->type())->formType(), $page->data());
    }

    private function resolveSelection(Book $book, ?string $requested): ?string
    {
        if (null !== $requested) {
            try {
                return $book->findPage($requested)->id();
            } catch (PageNotFoundException) {
                // Unknown id (stale link): fall back to the first page.
            }
        }

        $pages = $book->pages();

        return isset($pages[0]) ? $pages[0]->id() : null;
    }
}
