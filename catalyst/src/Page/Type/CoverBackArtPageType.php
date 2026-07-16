<?php

declare(strict_types=1);

namespace App\Page\Type;

use App\Design\ImageSource;
use App\Page\Form\CoverBackArtType;
use App\Page\PageTypeInterface;

/** Back cover (verso), épuré: full illustration front and center, tagline, call to action, QR. */
final class CoverBackArtPageType implements PageTypeInterface
{
    public function __construct(private readonly ImageSource $images)
    {
    }

    public function key(): string
    {
        return 'cover-back-art';
    }

    public function category(): string
    {
        return 'covers';
    }

    public function labelKey(): string
    {
        return 'page.cover_back_art.label';
    }

    public function descriptionKey(): string
    {
        return 'page.cover_back_art.description';
    }

    public function defaultData(): array
    {
        return [
            'eyebrow' => 'Aubaine',
            'tagline' => 'Déchaînez votre imagination',
            'cta' => 'Commencez votre aventure',
            'url' => 'aubaine.io',
            'image' => 'randome.png',
            'showQr' => true,
        ];
    }

    public function formType(): string
    {
        return CoverBackArtType::class;
    }

    public function template(): string
    {
        return 'print/pages/cover_back_art.html.twig';
    }

    public function buildViewModel(array $data): array
    {
        $view = array_merge($this->defaultData(), $data);
        $view['showQr'] = ($data['showQr'] ?? true) === true;

        return $view;
    }

    public function referencedContentPaths(array $data): array
    {
        $paths = [];
        $image = \is_string($data['image'] ?? null) ? $data['image'] : '';
        if ('' !== $image) {
            $paths[] = $this->images->path('covers', $image);
        }
        if (($data['showQr'] ?? true) === true) {
            $paths[] = $this->images->path('covers', 'qr-aubaine.png');
        }

        return $paths;
    }
}
