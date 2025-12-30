<?php

namespace App\Service;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\AbstractUid;

class DatabaseImporter
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly Filesystem $filesystem,
        private readonly string $dataDirectory
    ) {
    }

    /**
     * @return list<DatabaseImportResult>
     */
    public function import(): array
    {
        $metadataMap = $this->buildMetadataMap();
        $entityNames = $this->determineEntitiesToImport($metadataMap);

        $results = [];

        foreach ($entityNames as $entityName) {
            $metadata = $metadataMap[$entityName] ?? null;

            if ($metadata === null) {
                throw new \RuntimeException(sprintf('Unknown entity "%s" in manifest.', $entityName));
            }

            $rows = $this->readEntityData($entityName);
            $this->entityManager->createQuery(sprintf('DELETE FROM %s e', $metadata->getName()))->execute();

            $entities = $this->hydrateEntities($rows, $metadata);
            $this->entityManager->flush();

            $this->hydrateAssociations($entities, $rows, $metadata);
            $this->entityManager->flush();

            $results[] = new DatabaseImportResult($entityName, count($entities));
        }

        return $results;
    }

    /**
     * @return array<string, ClassMetadata<object>>
     */
    private function buildMetadataMap(): array
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $filtered = array_filter(
            $metadata,
            static fn (ClassMetadata $meta): bool => !$meta->isMappedSuperclass && !$meta->isEmbeddedClass
        );

        $map = [];

        foreach ($filtered as $meta) {
            $map[$meta->getReflectionClass()->getShortName()] = $meta;
        }

        ksort($map);

        return $map;
    }

    /**
     * @param array<string, ClassMetadata<object>> $metadataMap
     * @return list<string>
     */
    private function determineEntitiesToImport(array $metadataMap): array
    {
        $manifestPath = $this->dataDirectory . '/_meta.json';

        if ($this->filesystem->exists($manifestPath)) {
            $content = file_get_contents($manifestPath);
            if ($content === false) {
                throw new FileNotFoundException(sprintf('Unable to read manifest file at "%s".', $manifestPath));
            }

            $data = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

            if (!isset($data['entities']) || !is_array($data['entities'])) {
                throw new \RuntimeException('Manifest file is missing "entities" key.');
            }

            /** @var list<string> $entities */
            $entities = array_values(array_filter($data['entities'], 'is_string'));

            return $entities;
        }

        $files = glob($this->dataDirectory . '/*.json') ?: [];
        $entities = [];

        foreach ($files as $file) {
            $basename = basename($file, '.json');
            if ($basename === '_meta') {
                continue;
            }
            if (isset($metadataMap[$basename])) {
                $entities[] = $basename;
            }
        }

        sort($entities, SORT_STRING);

        return $entities;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readEntityData(string $entityName): array
    {
        $path = $this->dataDirectory . '/' . $entityName . '.json';

        if (!$this->filesystem->exists($path)) {
            throw new FileNotFoundException(sprintf('Data file for entity "%s" not found at "%s".', $entityName, $path));
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new FileNotFoundException(sprintf('Unable to read file "%s".', $path));
        }

        $data = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('Invalid JSON content for entity "%s".', $entityName));
        }

        return array_values($data);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<object>
     */
    private function hydrateEntities(array $rows, ClassMetadata $metadata): array
    {
        $entities = [];

        foreach ($rows as $row) {
            $entity = $metadata->newInstance();
            $this->setFieldValues($entity, $row, $metadata);

            $this->entityManager->persist($entity);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * @param list<object> $entities
     * @param list<array<string, mixed>> $rows
     */
    private function hydrateAssociations(array $entities, array $rows, ClassMetadata $metadata): void
    {
        $associations = $metadata->getAssociationNames();
        sort($associations);

        foreach ($entities as $index => $entity) {
            $row = $rows[$index] ?? [];

            foreach ($associations as $association) {
                if (!array_key_exists($association, $row)) {
                    continue;
                }

                $mapping = $metadata->getAssociationMapping($association);
                $targetClass = $mapping['targetEntity'];

                if ($metadata->isSingleValuedAssociation($association)) {
                    $identifier = $row[$association];
                    $related = $this->resolveRelatedEntity($targetClass, $identifier);
                    $this->propertyAccessor->setValue($entity, $association, $related);
                    continue;
                }

                $identifiers = is_array($row[$association]) ? $row[$association] : [];
                $collection = $this->propertyAccessor->getValue($entity, $association);

                if ($collection instanceof Collection) {
                    $collection->clear();
                } else {
                    $collection = new ArrayCollection();
                    $this->propertyAccessor->setValue($entity, $association, $collection);
                }

                foreach ($identifiers as $identifier) {
                    $related = $this->resolveRelatedEntity($targetClass, $identifier);
                    if ($related !== null && $collection instanceof Collection) {
                        $collection->add($related);
                    }
                }
            }
        }
    }

    private function setFieldValues(object $entity, array $data, ClassMetadata $metadata): void
    {
        $fields = $metadata->getFieldNames();
        sort($fields);

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $property = $metadata->getReflectionProperty($field);
            $value = $this->convertValueForField($data[$field], $property);

            if ($property->getType() instanceof \ReflectionNamedType && $property->getType()->getName() === 'array') {
                $property->setAccessible(true);
                $property->setValue($entity, $value);

                continue;
            }

            if (in_array($field, $metadata->getIdentifier(), true)) {
                $property->setAccessible(true);
                $property->setValue($entity, $value);
                continue;
            }

            $this->propertyAccessor->setValue($entity, $field, $value);
        }
    }

    private function resolveRelatedEntity(string $targetClass, mixed $identifier): ?object
    {
        if ($identifier === null || $identifier === '') {
            return null;
        }

        $targetMetadata = $this->entityManager->getClassMetadata($targetClass);

        foreach (['slug', 'code'] as $preferredField) {
            if ($targetMetadata->hasField($preferredField)) {
                $entity = $this->entityManager
                    ->getRepository($targetClass)
                    ->findOneBy([$preferredField => $identifier]);

                if ($entity !== null) {
                    return $entity;
                }
            }
        }

        $idValues = $targetMetadata->getIdentifierFieldNames();
        $criteria = [];
        $identifierParts = is_string($identifier) ? explode('|', $identifier) : (array) $identifier;

        foreach ($idValues as $index => $idField) {
            $idValue = $identifierParts[$index] ?? $identifier;
            $criteria[$idField] = $this->convertValueForField($idValue, $targetMetadata->getReflectionProperty($idField));
        }

        return $this->entityManager->getRepository($targetClass)->findOneBy($criteria);
    }

    private function convertValueForField(mixed $value, \ReflectionProperty $property): mixed
    {
        if ($value === null) {
            return null;
        }

        $type = $property->getType();

        if (!$type instanceof \ReflectionNamedType) {
            return $value;
        }

        if ($type->isBuiltin()) {
            return $value;
        }

        $typeName = $type->getName();

        if (is_subclass_of($typeName, \BackedEnum::class)) {
            /** @var \BackedEnum $typeName */
            return $typeName::from($value);
        }

        if (is_a($typeName, \DateTimeInterface::class, true)) {
            return new DateTimeImmutable((string) $value);
        }

        if (is_a($typeName, AbstractUid::class, true)) {
            /** @var class-string<AbstractUid> $typeName */
            return $typeName::fromString((string) $value);
        }

        return $value;
    }
}
