<?php

declare(strict_types=1);

namespace App\Book\Dto;

use App\Book\BookType;
use App\Book\Model\Book;
use Symfony\Component\Validator\Constraints as Assert;

/** Typed input for creating or editing a book's metadata. */
final class BookMeta
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 120)]
        public string $title = '',
        #[Assert\Length(max: 200)]
        public ?string $subtitle = null,
        public BookType $bookType = BookType::Archetype,
        #[Assert\PositiveOrZero]
        public int $versionMajor = 0,
        #[Assert\PositiveOrZero]
        public int $versionMinor = 1,
    ) {
    }

    public static function fromBook(Book $book): self
    {
        return new self(
            $book->title(),
            $book->subtitle(),
            $book->bookType(),
            $book->version()->major,
            $book->version()->minor,
        );
    }
}
