<?php

declare(strict_types=1);

namespace App\Page\Type;

use App\Page\Form\CoverFrontType;
use App\Page\PageTypeInterface;

/** Front cover (recto): dark cinematic art, gold title and filigree. */
final class CoverFrontPageType implements PageTypeInterface
{
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
            'version' => 'Archétype · v0.1',
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
        $view['ornaments'] = ($data['ornaments'] ?? true) == true;

        return $view;
    }
}
