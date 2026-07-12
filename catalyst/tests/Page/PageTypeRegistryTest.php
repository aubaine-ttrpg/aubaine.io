<?php

declare(strict_types=1);

namespace App\Tests\Page;

use App\Page\Exception\UnknownPageTypeException;
use App\Page\PageTypeRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PageTypeRegistryTest extends KernelTestCase
{
    public function testCollectsTaggedPageTypes(): void
    {
        self::bootKernel();
        $registry = self::getContainer()->get(PageTypeRegistry::class);
        self::assertInstanceOf(PageTypeRegistry::class, $registry);

        self::assertTrue($registry->has('cover-front'));
        self::assertTrue($registry->has('cover-back'));
        self::assertTrue($registry->has('skill-tree'));
        self::assertGreaterThanOrEqual(3, \count($registry->all()));
        self::assertArrayHasKey('covers', $registry->byCategory());
    }

    public function testUnknownKeyThrows(): void
    {
        self::bootKernel();
        $registry = self::getContainer()->get(PageTypeRegistry::class);
        self::assertInstanceOf(PageTypeRegistry::class, $registry);

        $this->expectException(UnknownPageTypeException::class);
        $registry->get('nope');
    }
}
