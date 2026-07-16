<?php

declare(strict_types=1);

namespace App\Book;

/**
 * What kind of document a book is. The value is a stable slug stored in the
 * book JSON; the label is resolved through translation (see `book.type.*`).
 */
enum BookType: string
{
    case Aventure = 'aventure';
    case Personnage = 'personnage';
    case Archetype = 'archetype';
    case Domaine = 'domaine';
    case FichePersonnage = 'fiche-personnage';

    /** Translation key for the human label, shown on the cover and in the form. */
    public function labelKey(): string
    {
        return 'book.type.'.match ($this) {
            self::Aventure => 'aventure',
            self::Personnage => 'personnage',
            self::Archetype => 'archetype',
            self::Domaine => 'domaine',
            self::FichePersonnage => 'fiche_personnage',
        };
    }
}
