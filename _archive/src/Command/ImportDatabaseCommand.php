<?php

namespace App\Command;

use App\Service\DatabaseImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'aubaine:database:import',
    description: 'Import Doctrine entities from JSON files under data/.'
)]
class ImportDatabaseCommand extends Command
{
    public function __construct(
        private readonly DatabaseImporter $databaseImporter
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Importing database');

        $start = microtime(true);

        try {
            $results = $this->databaseImporter->import();
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $duration = microtime(true) - $start;

        foreach ($results as $result) {
            $io->writeln(sprintf(
                '%s: %d rows',
                $result->getEntityName(),
                $result->getRowCount()
            ));
        }

        $io->success(sprintf('Import completed in %.2f s', $duration));

        return Command::SUCCESS;
    }
}
