<?php

declare(strict_types=1);

namespace App\Page\Type;

use App\Page\Form\CoverBackType;
use App\Page\PageTypeInterface;

/** Back cover (verso): full illustration, blurb, call to action, QR. */
final class CoverBackPageType implements PageTypeInterface
{
    public function key(): string
    {
        return 'cover-back';
    }

    public function category(): string
    {
        return 'covers';
    }

    public function labelKey(): string
    {
        return 'page.cover_back.label';
    }

    public function descriptionKey(): string
    {
        return 'page.cover_back.description';
    }

    public function defaultData(): array
    {
        return [
            'eyebrow' => 'Aubaine',
            'tagline' => 'Déchaînez votre imagination',
            'bodyText' => "Jouez au nouveau meilleur jeu de rôle du monde.\n"
                ."Façonnez votre personnage au fil des arbres de compétences.\n"
                ."Insufflez-lui tout ce que vous imaginez, sans classes ni limites.\n"
                ."Aventurez-vous dans les terres d'Éden, ou dans votre propre monde.",
            'cta' => 'Commencez votre aventure',
            'url' => 'aubaine.io',
            'copyright' => '© Aubaine · v0.1',
            'image' => 'randome.png',
            'showQr' => true,
            'ornaments' => true,
        ];
    }

    public function formType(): string
    {
        return CoverBackType::class;
    }

    public function template(): string
    {
        return 'print/pages/cover_back.html.twig';
    }

    public function buildViewModel(array $data): array
    {
        $view = array_merge($this->defaultData(), $data);

        $body = \is_string($view['bodyText'] ?? null) ? $view['bodyText'] : '';
        $lines = preg_split('/\r?\n/', $body) ?: [];
        $view['bodyLines'] = array_values(array_filter(
            array_map(static fn (string $line): string => trim($line), $lines),
            static fn (string $line): bool => '' !== $line,
        ));

        $view['showQr'] = ($data['showQr'] ?? true) == true;
        $view['ornaments'] = ($data['ornaments'] ?? true) == true;

        return $view;
    }
}
