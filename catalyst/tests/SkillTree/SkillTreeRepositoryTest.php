<?php

declare(strict_types=1);

namespace App\Tests\SkillTree;

use App\Design\Domain;
use App\SkillTree\Exception\SkillTreeNotFoundException;
use App\SkillTree\SkillTreeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SkillTreeRepositoryTest extends KernelTestCase
{
    private function repository(): SkillTreeRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(SkillTreeRepository::class);
        self::assertInstanceOf(SkillTreeRepository::class, $repository);

        return $repository;
    }

    public function testEverySeedTreeValidatesAndLoads(): void
    {
        $repository = $this->repository();
        $ids = $repository->ids();
        self::assertContains('feu', $ids);
        self::assertContains('berserker', $ids);

        foreach ($ids as $id) {
            $tree = $repository->load($id);
            self::assertNotEmpty($tree->nodes);
            self::assertContains($tree->size, [8, 16]);
        }
    }

    public function testFeuTreeShape(): void
    {
        $tree = $this->repository()->load('feu');
        self::assertSame('Feu', $tree->name);
        self::assertSame('domain', $tree->treeType->value);

        $ignite = null;
        foreach ($tree->nodes as $node) {
            if ('IGNIT-01' === $node->id) {
                $ignite = $node;
            }
        }

        self::assertNotNull($ignite);
        self::assertSame(5, $ignite->xp());
        self::assertSame(1, $ignite->energy);
        self::assertSame([Domain::Fire], $ignite->domains->all());
    }

    public function testMissingTreeThrows(): void
    {
        $this->expectException(SkillTreeNotFoundException::class);
        $this->repository()->load('does-not-exist');
    }
}
