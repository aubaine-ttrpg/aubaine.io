<?php

namespace App\Service;

class DatabaseImportResult
{
    public function __construct(
        private readonly string $entityName,
        private readonly int $rowCount
    ) {
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
