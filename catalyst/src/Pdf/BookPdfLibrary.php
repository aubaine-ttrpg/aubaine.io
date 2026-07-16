<?php

declare(strict_types=1);

namespace App\Pdf;

use App\Book\Model\Book;
use App\Pdf\Exception\PdfRenderingException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Sensiolabs\GotenbergBundle\Exception\ClientException;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Sensiolabs\GotenbergBundle\Processor\TempfileProcessor;

/**
 * The content-addressed PDF cache. A book renders to
 * `{bookName}_{webpackHash}_{bookHash}.pdf`; if that file already exists it is
 * served as-is, otherwise Gotenberg builds it and it is written atomically.
 * Because the filename carries both hashes, any change to the presentation code
 * or the book's content yields a different name and forces a rebuild, so a stale
 * PDF is never served. Older PDFs for the same book are pruned after a rebuild.
 */
final class BookPdfLibrary
{
    public function __construct(
        private readonly GotenbergPdfInterface $gotenberg,
        private readonly string $pdfDirectory,
        private readonly LoggerInterface $logger,
    ) {
    }

    /** Path to a ready PDF for this release: the cached file if present, else freshly built. */
    public function render(Book $book, BookRelease $release): string
    {
        $path = $this->pdfDirectory.'/'.$release->fileName($book->id());
        if (is_file($path)) {
            $this->logger->info('book.pdf_cache_hit', ['event' => 'book.pdf_cache_hit', 'book_id' => $book->id()]);

            return $path;
        }

        $this->ensureDirectory();
        $resource = $this->generate($book, $release);
        try {
            $this->writeAtomically($path, $resource);
        } finally {
            fclose($resource);
        }

        $this->prune($book->id(), basename($path));
        $this->logger->info('book.pdf_built', ['event' => 'book.pdf_built', 'book_id' => $book->id()]);

        return $path;
    }

    /**
     * @return resource a rewound stream of the generated PDF
     */
    private function generate(Book $book, BookRelease $release)
    {
        try {
            $stream = $this->gotenberg->url()
                ->route('app_book_print', [
                    'id' => $book->id(),
                    // Pass the pre-computed hashes so the printed back cover shows
                    // exactly what the filename encodes (no drift between the two).
                    'wp' => $release->webpackHash,
                    'bk' => $release->bookHash,
                ])
                ->printBackground()      // cover art, paper textures, dark cover backgrounds
                ->preferCssPageSize()    // honour the CSS @page A4 + margin:0 (full bleed)
                ->waitForExpression('window.__abilitiesPaginated === true') // set after fonts load + ability pages reflow
                ->waitDelay('300ms')     // settle margin for background-image paint
                ->generate()
                ->processor(new TempfileProcessor())
                ->process();
        } catch (ClientException $e) {
            $this->logger->error('book.pdf_render_failed', ['event' => 'book.pdf_render_failed', 'book_id' => $book->id()]);

            throw PdfRenderingException::forBook($book->id(), $e);
        }

        if (!\is_resource($stream)) {
            throw new RuntimeException('Gotenberg returned no PDF stream.');
        }

        return $stream;
    }

    /**
     * @param resource $resource
     */
    private function writeAtomically(string $path, $resource): void
    {
        $tmp = $path.'.'.bin2hex(random_bytes(4)).'.tmp';
        $dest = fopen($tmp, 'wb');
        if (false === $dest) {
            throw new RuntimeException(\sprintf('Unable to open a temp file for "%s".', $path));
        }

        $copied = stream_copy_to_stream($resource, $dest);
        fclose($dest);
        if (false === $copied || !rename($tmp, $path)) {
            @unlink($tmp);

            throw new RuntimeException(\sprintf('Unable to write the PDF "%s".', $path));
        }
    }

    /** Drop other cached PDFs for this book; only the current release is kept. */
    private function prune(string $bookId, string $keep): void
    {
        $files = glob($this->pdfDirectory.'/'.$bookId.'_*.pdf');
        foreach (\is_array($files) ? $files : [] as $file) {
            if (basename($file) !== $keep) {
                @unlink($file);
            }
        }
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->pdfDirectory) && !mkdir($this->pdfDirectory, 0o775, true) && !is_dir($this->pdfDirectory)) {
            throw new RuntimeException(\sprintf('Unable to create "%s".', $this->pdfDirectory));
        }
    }
}
