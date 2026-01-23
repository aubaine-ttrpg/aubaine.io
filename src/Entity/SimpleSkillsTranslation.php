<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

#[ORM\Entity(repositoryClass: \Gedmo\Translatable\Entity\Repository\TranslationRepository::class)]
#[ORM\Table(
    name: 'simple_skills_translations',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'simple_skills_lookup_unique_idx', columns: ['locale', 'object_class', 'field', 'foreign_key']),
    ],
    indexes: [
        new ORM\Index(name: 'simple_skills_translations_lookup_idx', columns: ['locale', 'object_class', 'field']),
        new ORM\Index(name: 'simple_skills_translation_object_idx', columns: ['object_class', 'foreign_key']),
    ],
)]
class SimpleSkillsTranslation extends AbstractTranslation
{
}
