<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

#[ORM\Entity(repositoryClass: \Gedmo\Translatable\Entity\Repository\TranslationRepository::class)]
#[ORM\Table(
    name: 'tag_translations',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'tag_lookup_unique_idx', columns: ['locale', 'object_class', 'field', 'foreign_key']),
    ],
    indexes: [
        new ORM\Index(name: 'tag_translations_lookup_idx', columns: ['locale', 'object_class', 'field']),
        new ORM\Index(name: 'tag_translation_object_idx', columns: ['object_class', 'foreign_key']),
    ],
)]
class TagTranslation extends AbstractTranslation
{
}
