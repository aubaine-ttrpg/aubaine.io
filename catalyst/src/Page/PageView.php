<?php

declare(strict_types=1);

namespace App\Page;

/** A book page resolved for rendering: which template and which view model. */
final readonly class PageView
{
    /**
     * @param array<string, mixed> $view
     */
    public function __construct(
        public string $pageId,
        public PageTypeInterface $type,
        public string $template,
        public array $view,
    ) {
    }
}
