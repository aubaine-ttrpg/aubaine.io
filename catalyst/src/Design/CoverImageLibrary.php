<?php

declare(strict_types=1);

namespace App\Design;

/**
 * Lists the cover artwork available to pick from (the seeded archetype art plus
 * anything a dev drops into assets/images/covers). Filenames are stored on the
 * page; a Twig helper resolves them to asset URLs at render time.
 */
final class CoverImageLibrary
{
    public function __construct(private readonly string $coversDirectory)
    {
    }

    /**
     * @return list<string>
     */
    public function filenames(): array
    {
        if (!is_dir($this->coversDirectory)) {
            return [];
        }

        $names = [];
        $files = glob($this->coversDirectory.'/*.{png,jpg,jpeg,webp}', \GLOB_BRACE);
        foreach (\is_array($files) ? $files : [] as $file) {
            $names[] = basename($file);
        }
        sort($names);

        return $names;
    }

    /**
     * Form choices: human label => filename.
     *
     * @return array<string, string>
     */
    public function choices(): array
    {
        $choices = [];
        foreach ($this->filenames() as $filename) {
            $label = ucfirst(str_replace(['-', '_'], ' ', pathinfo($filename, \PATHINFO_FILENAME)));
            $choices[$label] = $filename;
        }

        return $choices;
    }
}
