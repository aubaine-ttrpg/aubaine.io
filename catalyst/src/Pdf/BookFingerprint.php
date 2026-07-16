<?php

declare(strict_types=1);

namespace App\Pdf;

use App\Book\BookSerializer;
use App\Book\Model\Book;
use App\Page\PageTypeRegistry;
use App\Pdf\Exception\AssetsNotBuiltException;
use Psr\Log\LoggerInterface;

/**
 * Computes the two identifiers bound to a generated book, before rendering, so
 * the printed version stamp and the cached PDF filename can never drift:
 *
 * - the webpack hash: over the bytes of every CSS/JS file the print bundle
 *   loads, so any change to the presentation code moves it (global per build);
 * - the book hash: over the canonical book JSON plus the bytes of every file
 *   the book's pages link to, so any content change (including editing the
 *   inside of a linked file, or swapping one for different bytes under the same
 *   name) moves it.
 */
final class BookFingerprint
{
    /** Short, readable hash length, matching the repo's `[hash:8]` asset convention. */
    private const int HASH_LENGTH = 8;

    private ?string $webpackHash = null;

    public function __construct(
        private readonly string $buildDirectory,
        private readonly BookSerializer $serializer,
        private readonly PageTypeRegistry $pageTypes,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function release(Book $book): BookRelease
    {
        return new BookRelease($book->version(), $this->webpackHash(), $this->bookHash($book));
    }

    /** Hash of every CSS/JS file the `print` entrypoint pulls in. Memoized per request. */
    public function webpackHash(): string
    {
        if (null !== $this->webpackHash) {
            return $this->webpackHash;
        }

        $entrypoints = $this->buildDirectory.'/entrypoints.json';
        $raw = is_file($entrypoints) ? file_get_contents($entrypoints) : false;
        if (false === $raw) {
            throw AssetsNotBuiltException::missing($entrypoints);
        }

        $decoded = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
        $print = \is_array($decoded) && \is_array($decoded['entrypoints'] ?? null) && \is_array($decoded['entrypoints']['print'] ?? null)
            ? $decoded['entrypoints']['print']
            : [];

        $urls = array_merge(
            $this->stringList($print['css'] ?? []),
            $this->stringList($print['js'] ?? []),
        );
        sort($urls);

        $public = \dirname($this->buildDirectory);
        $blobs = [];
        foreach ($urls as $url) {
            $blobs[$url] = $this->read($public.$url);
        }

        return $this->webpackHash = $this->digest($blobs);
    }

    /** Hash of the book's canonical JSON plus the bytes of every linked file. */
    public function bookHash(Book $book): string
    {
        $blobs = ['@book' => $this->serializer->toCanonicalJson($book)];

        foreach ($book->pages() as $page) {
            $type = $this->pageTypes->get($page->type());
            foreach ($type->referencedContentPaths($page->data()) as $path) {
                $blobs[$path] = $this->read($path);
            }
        }

        return $this->digest($blobs);
    }

    /**
     * Read a linked file's bytes. A missing or unreadable reference is recorded
     * as an empty blob (still keyed by path, so removing/adding it moves the
     * hash) and logged, so a broken reference degrades instead of crashing.
     */
    private function read(string $path): string
    {
        $bytes = is_file($path) ? file_get_contents($path) : false;
        if (false === $bytes) {
            $this->logger->warning('book.fingerprint_missing_file', ['event' => 'book.fingerprint_missing_file', 'path' => $path]);

            return '';
        }

        return $bytes;
    }

    /**
     * @param array<string, string> $blobs path => bytes
     */
    private function digest(array $blobs): string
    {
        ksort($blobs);
        $payload = '';
        foreach ($blobs as $key => $bytes) {
            $payload .= $key."\0".hash('sha256', $bytes)."\0";
        }

        return substr(hash('sha256', $payload), 0, self::HASH_LENGTH);
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        $strings = [];
        foreach (\is_array($value) ? $value : [] as $item) {
            if (\is_string($item)) {
                $strings[] = $item;
            }
        }

        return $strings;
    }
}
