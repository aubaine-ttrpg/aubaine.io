<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Book\BookEditor;
use App\Book\BookRepository;
use Closure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * The "Download PDF" / "See PDF" journey. Gotenberg is faked at the transport
 * layer (the `gotenberg.client` scoped client), so the real controller and the
 * bundle's URL builder run without a live Gotenberg container.
 */
final class BookPdfTest extends WebTestCase
{
    /** The multipart payload the controller sent Gotenberg, captured by the transport fake. */
    private string $lastGotenbergBody = '';

    public function testDownloadPdfSendsAttachmentWithBookSlugFilename(): void
    {
        $client = static::createClient();
        $this->fakeGotenberg();
        $book = $this->createBook('The Blade of Fire');

        try {
            $client->request('GET', '/books/'.$book->id().'/pdf?download=1');

            self::assertResponseIsSuccessful();
            self::assertResponseHeaderSame('content-type', 'application/pdf');
            $disposition = (string) $client->getResponse()->headers->get('content-disposition');
            self::assertStringStartsWith('attachment', $disposition);
            self::assertStringContainsString('the-blade-of-fire.pdf', $disposition);
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testViewPdfSendsInlineDisposition(): void
    {
        $client = static::createClient();
        $this->fakeGotenberg();
        $book = $this->createBook('Preview Me');

        try {
            $client->request('GET', '/books/'.$book->id().'/pdf');

            self::assertResponseIsSuccessful();
            self::assertResponseHeaderSame('content-type', 'application/pdf');
            self::assertStringStartsWith('inline', (string) $client->getResponse()->headers->get('content-disposition'));
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testPdfRenderIsGatedOnFontsAndSettleDelay(): void
    {
        $client = static::createClient();
        $this->fakeGotenberg();
        $book = $this->createBook('Gated');

        try {
            $client->request('GET', '/books/'.$book->id().'/pdf');

            self::assertResponseIsSuccessful();
            self::assertStringContainsString("document.fonts.status === 'loaded'", $this->lastGotenbergBody);
            self::assertStringContainsString('300ms', $this->lastGotenbergBody);
        } finally {
            $this->deleteBook($book->id());
        }
    }

    public function testMissingBookReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/books/does-not-exist/pdf');

        self::assertResponseStatusCodeSame(404);
    }

    public function testRendererFailureMapsTo502(): void
    {
        $client = static::createClient();
        $this->fakeGotenberg(convertStatus: 500);
        $book = $this->createBook('Broken');

        try {
            $client->request('GET', '/books/'.$book->id().'/pdf');

            self::assertResponseStatusCodeSame(502);
        } finally {
            $this->deleteBook($book->id());
        }
    }

    /**
     * Stubs the Gotenberg transport: a valid version for the bundle's debug
     * `/version` probe, and for the convert endpoint either a canned PDF (that
     * echoes the requested output filename, like Gotenberg does) or an error.
     */
    private function fakeGotenberg(int $convertStatus = 200): void
    {
        $mock = new MockHttpClient(function (string $method, string $url, array $options) use ($convertStatus): MockResponse {
            if (str_ends_with($url, '/version')) {
                return new MockResponse('8.5.0');
            }

            $this->lastGotenbergBody = self::collectBody($options['body'] ?? '');

            if (200 !== $convertStatus) {
                return new MockResponse('render failed', ['http_code' => $convertStatus]);
            }

            $name = 'document';
            foreach ($options['normalized_headers']['gotenberg-output-filename'] ?? [] as $header) {
                $name = trim(substr($header, (int) strpos($header, ':') + 1));
            }

            return new MockResponse('%PDF-1.4 test', [
                'http_code' => 200,
                'response_headers' => [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="'.$name.'.pdf"',
                ],
            ]);
        });

        static::getContainer()->set('gotenberg.client', $mock);
    }

    /**
     * Drains the request body HttpClient hands the transport (a chunk closure for
     * the bundle's multipart stream, or a plain string) into one string to assert on.
     */
    private static function collectBody(mixed $body): string
    {
        if ($body instanceof Closure) {
            $collected = '';
            while ('' !== ($chunk = $body(8192))) {
                $collected .= $chunk;
            }

            return $collected;
        }

        if (is_iterable($body)) {
            $collected = '';
            foreach ($body as $chunk) {
                $collected .= (string) $chunk;
            }

            return $collected;
        }

        return \is_string($body) ? $body : '';
    }

    private function createBook(string $title): \App\Book\Model\Book
    {
        $editor = static::getContainer()->get(BookEditor::class);
        self::assertInstanceOf(BookEditor::class, $editor);

        return $editor->create($title, null);
    }

    private function deleteBook(string $id): void
    {
        $repository = static::getContainer()->get(BookRepository::class);
        self::assertInstanceOf(BookRepository::class, $repository);
        $repository->delete($id);
    }
}
