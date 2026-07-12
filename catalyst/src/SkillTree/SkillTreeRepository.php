<?php

declare(strict_types=1);

namespace App\SkillTree;

use App\Design\DomainSet;
use App\Design\NodeType;
use App\SkillTree\Exception\InvalidSkillTreeException;
use App\SkillTree\Exception\SkillTreeNotFoundException;
use App\SkillTree\Model\Position;
use App\SkillTree\Model\SkillNode;
use App\SkillTree\Model\SkillTree;
use App\SkillTree\Model\TreeCore;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use stdClass;

/**
 * Reads the seed skill-tree JSON files, validates each against
 * skilltree.schema.json, and maps it to the {@see SkillTree} model. These are
 * game-data inputs the skill-tree page consumes, kept separate from books.
 */
final class SkillTreeRepository
{
    public function __construct(
        private readonly string $treesDirectory,
        private readonly string $schemaPath,
    ) {
    }

    /**
     * @return list<string>
     */
    public function ids(): array
    {
        if (!is_dir($this->treesDirectory)) {
            return [];
        }

        $ids = [];
        foreach (glob($this->treesDirectory.'/*.json') ?: [] as $file) {
            $ids[] = basename($file, '.json');
        }
        sort($ids);

        return $ids;
    }

    /**
     * Lightweight list for the tree picker.
     *
     * @return list<array{id: string, name: string, treeType: string}>
     */
    public function summaries(): array
    {
        $summaries = [];
        foreach ($this->ids() as $id) {
            $tree = $this->load($id);
            $summaries[] = ['id' => $tree->id, 'name' => $tree->name, 'treeType' => $tree->treeType->value];
        }

        return $summaries;
    }

    public function has(string $id): bool
    {
        return 1 === preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id) && is_file($this->path($id));
    }

    public function load(string $id): SkillTree
    {
        if (!$this->has($id)) {
            throw SkillTreeNotFoundException::forId($id);
        }

        $raw = file_get_contents($this->path($id));
        if (false === $raw) {
            throw SkillTreeNotFoundException::forId($id);
        }

        $this->validate($raw, $id);

        $data = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
        if (!\is_array($data)) {
            throw InvalidSkillTreeException::forId($id, 'root is not an object');
        }

        /* @var array<string, mixed> $data */
        return $this->mapTree($data);
    }

    private function validate(string $raw, string $id): void
    {
        $validator = new Validator();
        $schema = json_decode((string) file_get_contents($this->schemaPath), false);
        if (!$schema instanceof stdClass) {
            throw InvalidSkillTreeException::forId($id, 'schema file is not a JSON object');
        }
        // opis requires an absolute root id; the shipped schema uses a relative one.
        $schema->{'$id'} = 'https://aubaine.io/schema/skilltree.json';

        $data = json_decode($raw, false);

        $result = $validator->validate($data, $schema);
        if ($result->isValid()) {
            return;
        }

        $error = $result->error();
        $detail = null !== $error
            ? implode('; ', array_keys((new ErrorFormatter())->format($error)))
            : 'unknown validation error';

        throw InvalidSkillTreeException::forId($id, $detail);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function mapTree(array $data): SkillTree
    {
        $nodes = [];
        foreach ($this->toList($data['nodes'] ?? []) as $rawNode) {
            $nodes[] = $this->mapNode($this->toArray($rawNode));
        }

        return new SkillTree(
            $this->toString($data['id'] ?? '') ?? '',
            $this->toString($data['name'] ?? '') ?? '',
            TreeType::from($this->toString($data['treeType'] ?? 'domain') ?? 'domain'),
            \is_int($data['size'] ?? null) ? $data['size'] : 16,
            $this->mapCore($this->toArray($data['core'] ?? [])),
            $nodes,
        );
    }

    /**
     * @param array<string, mixed> $core
     */
    private function mapCore(array $core): TreeCore
    {
        return new TreeCore(
            $this->toString($core['label'] ?? '') ?? '',
            $this->toString($core['sublabel'] ?? null),
            DomainSet::fromKeys($this->toStringList($core['domains'] ?? [])),
            $this->mapPosition($core['pos'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $node
     */
    private function mapNode(array $node): SkillNode
    {
        $energy = $node['energy'] ?? null;

        return new SkillNode(
            $this->toString($node['id'] ?? '') ?? '',
            $this->toString($node['title'] ?? '') ?? '',
            NodeType::from($this->toString($node['type'] ?? 'passive') ?? 'passive'),
            \is_int($node['tier'] ?? null) ? $node['tier'] : 1,
            DomainSet::fromKeys($this->toStringList($node['domains'] ?? [])),
            $this->toString($node['description'] ?? '') ?? '',
            $this->mapPosition($node['pos'] ?? null),
            $this->toStringList($node['linked'] ?? []),
            $this->toString($node['icon'] ?? null),
            ($node['ultimate'] ?? null) === true,
            $this->toString($node['activation'] ?? null),
            $this->toString($node['range'] ?? null),
            $this->toString($node['duration'] ?? null),
            ($node['concentration'] ?? null) === true,
            \is_int($energy) ? $energy : null,
            $this->toStringList($node['tags'] ?? []),
            $this->toString($node['evolvesFrom'] ?? null),
        );
    }

    private function mapPosition(mixed $value): ?Position
    {
        $pos = $this->toArray($value);
        $x = $pos['x'] ?? null;
        $y = $pos['y'] ?? null;
        if (is_numeric($x) && is_numeric($y)) {
            return new Position((float) $x, (float) $y);
        }

        return null;
    }

    private function toString(mixed $value): ?string
    {
        return \is_string($value) ? $value : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(mixed $value): array
    {
        /* @var array<string, mixed> */
        return \is_array($value) ? $value : [];
    }

    /**
     * @return list<mixed>
     */
    private function toList(mixed $value): array
    {
        return \is_array($value) ? array_values($value) : [];
    }

    /**
     * @return list<string>
     */
    private function toStringList(mixed $value): array
    {
        $strings = [];
        foreach ($this->toList($value) as $item) {
            if (\is_string($item)) {
                $strings[] = $item;
            }
        }

        return $strings;
    }

    private function path(string $id): string
    {
        return $this->treesDirectory.'/'.$id.'.json';
    }
}
