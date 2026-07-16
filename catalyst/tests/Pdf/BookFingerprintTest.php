<?php

declare(strict_types=1);

namespace App\Tests\Pdf;

use App\Book\BookSerializer;
use App\Book\BookType;
use App\Book\Model\Book;
use App\Book\Model\Page;
use App\Book\Version;
use App\Page\PageTypeInterface;
use App\Page\PageTypeRegistry;
use App\Pdf\BookFingerprint;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class BookFingerprintTest extends TestCase
{
    private string $root;
    private string $buildDir;
    private string $linkedFile;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir().'/catalyst-fp-'.bin2hex(random_bytes(4));
        $this->buildDir = $this->root.'/build';
        mkdir($this->buildDir, 0o775, true);
        file_put_contents($this->buildDir.'/entrypoints.json', (string) json_encode([
            'entrypoints' => ['print' => ['js' => ['/build/print.js'], 'css' => ['/build/print.css']]],
        ]));
        file_put_contents($this->buildDir.'/print.css', '.a{color:red}');
        file_put_contents($this->buildDir.'/print.js', 'console.log(1)');

        $this->linkedFile = $this->root.'/linked.json';
        file_put_contents($this->linkedFile, '{"nodes":1}');
    }

    protected function tearDown(): void
    {
        foreach ([$this->buildDir.'/entrypoints.json', $this->buildDir.'/print.css', $this->buildDir.'/print.js', $this->linkedFile] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        foreach ([$this->buildDir, $this->root] as $dir) {
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
    }

    public function testWebpackHashIsStableAndEightHex(): void
    {
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $this->fingerprint()->webpackHash());
        self::assertSame($this->fingerprint()->webpackHash(), $this->fingerprint()->webpackHash());
    }

    public function testWebpackHashChangesWhenPrintCssChanges(): void
    {
        $before = $this->fingerprint()->webpackHash();

        file_put_contents($this->buildDir.'/print.css', '.a{color:blue}');

        self::assertNotSame($before, $this->fingerprint()->webpackHash());
    }

    public function testBookHashIsStableForTheSameBook(): void
    {
        $book = $this->book(new Version(0, 1));

        self::assertSame($this->fingerprint()->bookHash($book), $this->fingerprint()->bookHash($book));
    }

    public function testBookHashChangesWhenTheBookJsonChanges(): void
    {
        $before = $this->fingerprint()->bookHash($this->book(new Version(0, 1)));
        $after = $this->fingerprint()->bookHash($this->book(new Version(0, 2)));

        self::assertNotSame($before, $after);
    }

    public function testBookHashChangesWhenALinkedFilesBytesChange(): void
    {
        $book = $this->book(new Version(0, 1));
        $before = $this->fingerprint()->bookHash($book);

        // Same filename, different bytes: the classic "stale content" case.
        file_put_contents($this->linkedFile, '{"nodes":2}');

        self::assertNotSame($before, $this->fingerprint()->bookHash($book));
    }

    private function fingerprint(): BookFingerprint
    {
        return new BookFingerprint($this->buildDir, new BookSerializer(), new PageTypeRegistry([$this->pageType()]), new NullLogger());
    }

    private function book(Version $version): Book
    {
        $at = new DateTimeImmutable('2026-01-01T00:00:00+00:00');

        return new Book('b', 'B', null, BookType::Archetype, $version, $at, $at, [new Page('p', 'fake', [])]);
    }

    /** A page type that links one controllable file, so the linked-content path can be tested. */
    private function pageType(): PageTypeInterface
    {
        return new class($this->linkedFile) implements PageTypeInterface {
            public function __construct(private readonly string $file)
            {
            }

            public function key(): string
            {
                return 'fake';
            }

            public function category(): string
            {
                return 'covers';
            }

            public function labelKey(): string
            {
                return 'x';
            }

            public function descriptionKey(): string
            {
                return 'x';
            }

            public function defaultData(): array
            {
                return [];
            }

            public function formType(): string
            {
                return TextType::class;
            }

            public function template(): string
            {
                return 't';
            }

            public function buildViewModel(array $data): array
            {
                return $data;
            }

            public function referencedContentPaths(array $data): array
            {
                return [$this->file];
            }
        };
    }
}
