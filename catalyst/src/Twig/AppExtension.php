<?php

declare(strict_types=1);

namespace App\Twig;

use App\Page\PageTypeInterface;
use App\Page\PageTypeRegistry;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** Thin bridge for things templates can't get from plain objects. */
final class AppExtension extends AbstractExtension
{
    public function __construct(
        private readonly PageTypeRegistry $registry,
        private readonly Packages $assets,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('page_type', $this->pageType(...)),
            new TwigFunction('catalyst_image', $this->image(...)),
        ];
    }

    public function pageType(string $key): PageTypeInterface
    {
        return $this->registry->get($key);
    }

    /** Resolve a content-image filename to its built asset URL. */
    public function image(string $category, ?string $filename): ?string
    {
        if (null === $filename || '' === $filename) {
            return null;
        }

        return $this->assets->getUrl('build/images/'.$category.'/'.$filename);
    }
}
