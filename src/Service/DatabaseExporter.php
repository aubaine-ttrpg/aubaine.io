<?php

namespace App\Service;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\AbstractUid;

class DatabaseExporter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Filesystem $filesystem,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly string $dataDirectory
    ) {
    }

    /**
     * @return list<DatabaseExportResult>
     */
    public function export(): array
    {
        $metadataCollection = $this->getExportableMetadata();
        $this->filesystem->mkdir($this->dataDirectory);

        $results = [];
        $entityNames = [];

        foreach ($metadataCollection as $metadata) {
            $entityName = $metadata->getReflectionClass()->getShortName();
            $rows = $this->exportEntity($metadata);

            $this->writeEntityFile($entityName, $rows);
            $results[] = new DatabaseExportResult($entityName, count($rows));
            $entityNames[] = $entityName;
        }

        $this->writeManifest($entityNames);

        return $results;
    }

    /**
     * @return list<ClassMetadata<object>>
     */
    private function getExportableMetadata(): array
    {
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $exportable = array_filter(
            $allMetadata,
            static fn (ClassMetadata $metadata): bool => !$metadata->isMappedSuperclass && !$metadata->isEmbeddedClass
        );

        usort(
            $exportable,
            static fn (ClassMetadata $a, ClassMetadata $b): int => strcmp(
                $a->getReflectionClass()->getShortName(),
                $b->getReflectionClass()->getShortName()
            )
        );

        return array_values($exportable);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function exportEntity(ClassMetadata $metadata): array
    {
        $repository = $this->entityManager->getRepository($metadata->getName());
        $entities = $repository->findAll();

        $rows = [];

        foreach ($entities as $entity) {
            $rows[] = [
                'data' => $this->normalizeEntity($entity, $metadata),
                'sort' => $this->resolveSortKey($entity, $metadata),
            ];
        }

        usort(
            $rows,
            static fn (array $a, array $b): int => strcmp((string) $a['sort'], (string) $b['sort'])
        );

        return array_map(
            static fn (array $row): array => $row['data'],
            $rows
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeEntity(object $entity, ClassMetadata $metadata): array
    {
        $normalized = [];

        $fields = $metadata->getFieldNames();
        sort($fields);

        foreach ($fields as $field) {
            $value = $this->propertyAccessor->getValue($entity, $field);
            $normalized[$field] = $this->normalizeValue($value);
        }

        $associations = $metadata->getAssociationNames();
        sort($associations);

        foreach ($associations as $association) {
            $mapping = $metadata->getAssociationMapping($association);
            $targetClass = $mapping['targetEntity'];

            if ($metadata->isSingleValuedAssociation($association)) {
                $value = $this->propertyAccessor->getValue($entity, $association);
                $normalized[$association] = $this->normalizeAssociationValue($value, $targetClass);
                continue;
            }

            $collection = $this->propertyAccessor->getValue($entity, $association);
            $identifiers = [];

            if ($collection instanceof Collection || is_iterable($collection)) {
                foreach ($collection as $related) {
                    $identifiers[] = $this->normalizeAssociationValue($related, $targetClass);
                }
            }

            $identifiers = array_values(array_filter(
                $identifiers,
                static fn (?string $value): bool => $value !== null
            ));

            sort($identifiers, SORT_STRING);

            $normalized[$association] = $identifiers;
        }

        return $normalized;
    }

    private function normalizeAssociationValue(?object $entity, string $targetClass): ?string
    {
        if ($entity === null) {
            return null;
        }

        $metadata = $this->entityManager->getClassMetadata($targetClass);

        return $this->resolveIdentifierValue($entity, $metadata);
    }

    private function resolveSortKey(object $entity, ClassMetadata $metadata): string
    {
        return $this->resolveIdentifierValue($entity, $metadata);
    }

    private function resolveIdentifierValue(object $entity, ClassMetadata $metadata): string
    {
        foreach (['slug', 'code'] as $preferredField) {
            if ($metadata->hasField($preferredField)) {
                $value = $this->propertyAccessor->getValue($entity, $preferredField);

                if ($value !== null) {
                    return (string) $this->normalizeValue($value);
                }
            }
        }

        $identifierValues = $metadata->getIdentifierValues($entity);
        $normalized = array_map(
            fn (mixed $value): mixed => $this->normalizeValue($value),
            $identifierValues
        );

        return implode('|', array_map(
            static fn (mixed $value): string => (string) $value,
            $normalized
        ));
    }

    private function writeEntityFile(string $entityName, array $rows): void
    {
        $this->filesystem->dumpFile(
            $this->dataDirectory . '/' . $entityName . '.json',
            json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @param list<string> $entityNames
     */
    private function writeManifest(array $entityNames): void
    {
        $manifest = [
            'exportedAt' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM),
            'entities' => $entityNames,
        ];

        $this->filesystem->dumpFile(
            $this->dataDirectory . '/_meta.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
        );
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->normalizeValue($item), $value);
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if ($value instanceof AbstractUid) {
            return (string) $value;
        }

        if (is_object($value)) {
            return (string) $value;
        }

        return $value;
    }
}
