<?php

declare(strict_types=1);

namespace App\Tests\SkillTree\Model;

use App\Design\DomainSet;
use App\Design\NodeType;
use App\SkillTree\Model\SkillNode;
use PHPUnit\Framework\TestCase;

final class SkillNodeTest extends TestCase
{
    public function testIconFileFallsBackToThePlaceholderWhenNoneAssigned(): void
    {
        self::assertSame(SkillNode::PLACEHOLDER_ICON, $this->node(null)->iconFile());
    }

    public function testIconFileReturnsTheAssignedFilename(): void
    {
        self::assertSame('aura-bravoure.png', $this->node('aura-bravoure.png')->iconFile());
    }

    private function node(?string $icon): SkillNode
    {
        return new SkillNode(
            'N1',
            'Test node',
            NodeType::Passive,
            1,
            DomainSet::fromKeys([]),
            'A node used only to exercise icon resolution.',
            null,
            [],
            $icon,
            null,
            null,
            null,
            false,
            null,
            [],
            null,
            [],
        );
    }
}
