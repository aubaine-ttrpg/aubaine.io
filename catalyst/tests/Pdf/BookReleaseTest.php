<?php

declare(strict_types=1);

namespace App\Tests\Pdf;

use App\Book\Version;
use App\Pdf\BookRelease;
use PHPUnit\Framework\TestCase;

final class BookReleaseTest extends TestCase
{
    public function testShortIsMajorMinorOnly(): void
    {
        $release = new BookRelease(new Version(0, 1), 'a1b2c3d4', 'e5f6a7b8');

        self::assertSame('v0.1', $release->short());
    }

    public function testFullAppendsBothHashes(): void
    {
        $release = new BookRelease(new Version(2, 13), 'a1b2c3d4', 'e5f6a7b8');

        self::assertSame('v2.13.a1b2c3d4.e5f6a7b8', $release->full());
    }

    public function testFileNameIsBookNameThenHashes(): void
    {
        $release = new BookRelease(new Version(0, 1), 'a1b2c3d4', 'e5f6a7b8');

        self::assertSame('druide_a1b2c3d4_e5f6a7b8.pdf', $release->fileName('druide'));
    }
}
