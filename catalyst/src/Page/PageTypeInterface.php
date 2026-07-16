<?php

declare(strict_types=1);

namespace App\Page;

/**
 * A precreated, designed page the user can add to a book and then customize.
 *
 * Implementations are auto-registered (see services.yaml `_instanceof`) and
 * collected by {@see PageTypeRegistry}, so adding a new page to the catalog is
 * a drop-in: a class implementing this interface plus a print template.
 *
 * A page type may emit one or several physical print pages; that is decided by
 * its template (a covers page emits one leaf, the skill tree emits its planche
 * plus paginated ability pages).
 */
interface PageTypeInterface
{
    /** Stable machine key stored on each {@see \App\Book\Model\Page}. */
    public function key(): string;

    /** Grouping shown in the catalog, e.g. "covers" or "trees". */
    public function category(): string;

    /** Translation key for the catalog tile title. */
    public function labelKey(): string;

    /** Translation key for the catalog tile description. */
    public function descriptionKey(): string;

    /**
     * Seed data stored when the page is first added.
     *
     * @return array<string, mixed>
     */
    public function defaultData(): array;

    /**
     * FQCN of the Symfony form type used to customize this page's data.
     *
     * @return class-string<\Symfony\Component\Form\FormTypeInterface<mixed>>
     */
    public function formType(): string;

    /** Print template that renders the page's physical leaf/leaves. */
    public function template(): string;

    /**
     * Turn stored data into the variables the print template expects
     * (resolving defaults, loading referenced resources, etc.).
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function buildViewModel(array $data): array;

    /**
     * Absolute paths of the source files this page renders from but only
     * references by name (linked data files and image assets). Used to
     * fingerprint the book's content so that editing a linked file, or swapping
     * one for different bytes under the same name, changes the book hash.
     * Return [] when the page renders from its stored data alone.
     *
     * @param array<string, mixed> $data
     *
     * @return list<string>
     */
    public function referencedContentPaths(array $data): array;
}
