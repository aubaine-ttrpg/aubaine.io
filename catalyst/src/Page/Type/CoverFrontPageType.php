<?php

declare(strict_types=1);

namespace App\Page\Type;

use App\Design\ImageSource;
use App\Page\Form\CoverFrontType;
use App\Page\PageTypeInterface;

/** Front cover (recto): dark cinematic art, gold title and filigree. */
final class CoverFrontPageType implements PageTypeInterface
{
    public function __construct(private readonly ImageSource $images)
    {
    }

    public function key(): string
    {
        return 'cover-front';
    }

    public function category(): string
    {
        return 'covers';
    }

    public function labelKey(): string
    {
        return 'page.cover_front.label';
    }

    public function descriptionKey(): string
    {
        return 'page.cover_front.description';
    }

    public function defaultData(): array
    {
        return [
            'eyebrow' => 'Aubaine',
            'title' => 'Parangon',
            'subtitle' => 'Les protecteurs de la Weitzguard',
            'image' => 'parangon.png',
            'ornaments' => true,
        ];
    }

    public function formType(): string
    {
        return CoverFrontType::class;
    }

    public function template(): string
    {
        return 'print/pages/cover_front.html.twig';
    }

    public function buildViewModel(array $data): array
    {
        $view = array_merge($this->defaultData(), $data);
        $view['ornaments'] = ($data['ornaments'] ?? true) === true;
        $view['titleClass'] = $this->titleClass(\is_string($view['title'] ?? null) ? $view['title'] : '');

        return $view;
    }

    public function referencedContentPaths(array $data): array
    {
        $image = \is_string($data['image'] ?? null) ? $data['image'] : '';

        return '' !== $image ? [$this->images->path('covers', $image)] : [];
    }

    /**
     * Longer titles step down a size so they stay inside the cover frame,
     * computed here rather than in Twig (print-safe, no JS fit-to-width).
     */
    private function titleClass(string $title): string
    {
        return match (true) {
            mb_strlen(trim($title)) >= 15 => 'cover__title--xlong',
            mb_strlen(trim($title)) >= 12 => 'cover__title--long',
            default => '',
        };
    }
}
