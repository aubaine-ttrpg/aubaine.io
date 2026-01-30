<?php

namespace App\Twig\Component;

use App\Entity\Skills;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('SkillExportCard')]
final class SkillExportCard
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public Skills $skill;
    public bool $displayCode = false;
    public string $exportLocale = 'en';

    /**
     * @return list<string>
     */
    public function getTagsList(): array
    {
        $raw = $this->skill->getTags();
        $tags = [];

        if (is_string($raw) && $raw !== '') {
            $normalized = str_replace(["\r\n", "\n", "\r"], ',', $raw);
            foreach (explode(',', $normalized) as $chunk) {
                $tag = trim($chunk);
                if ($tag !== '') {
                    $tags[] = $tag;
                }
            }
        }

        $unique = [];
        foreach ($tags as $tag) {
            $key = strtolower($tag);
            if (!array_key_exists($key, $unique)) {
                $unique[$key] = $tag;
            }
        }

        return array_values($unique);
    }

    public function getEnergyValue(): ?string
    {
        $energy = $this->skill->getEnergy();
        if ($energy === null) {
            return null;
        }

        $energy = trim($energy);
        if ($energy === '') {
            return null;
        }

        $numeric = str_replace(',', '.', $energy);
        if (is_numeric($numeric) && (float) $numeric === 0.0) {
            return null;
        }

        return $energy;
    }
}
