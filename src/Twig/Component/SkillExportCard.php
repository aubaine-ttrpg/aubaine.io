<?php

namespace App\Twig\Component;

use App\Entity\Skills;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('SkillExportCard')]
final class SkillExportCard
{
    public Skills $skill;
    public bool $displayCode = false;
    public string $exportLocale = 'en';
    public bool $hideEmptyPrerequisites = false;

    public function getTagsLine(): string
    {
        $raw = $this->skill->getTags();
        return is_string($raw) ? $raw : '';
    }
}
