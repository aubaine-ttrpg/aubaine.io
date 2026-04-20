<?php

namespace App\Command;

use App\Service\DatabaseExporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'aubaine:database:export',
    description: 'Export Doctrine entities to JSON files under data/.'
)]
class ExportDatabaseCommand extends Command
{
    public function __construct(
        private readonly DatabaseExporter $databaseExporter
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Exporting database');

        $start = microtime(true);

        try {
            $results = $this->databaseExporter->export();
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

        $io->success(sprintf('Export completed in %.2f s', $duration));

        return Command::SUCCESS;
    }
}
