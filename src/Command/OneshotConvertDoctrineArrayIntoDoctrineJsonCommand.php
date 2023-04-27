<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:oneshot:convert-doctrine-array-into-doctrine-json',
    description: 'One shot command for converting raw data in Doctrine entities from "array" type into "json" type.',
    hidden: true,
)]
class OneshotConvertDoctrineArrayIntoDoctrineJsonCommand extends Command
{
    public function __construct(private Connection $connection)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fetching radio tables…');

        $radioTables = $this->connection
            ->executeQuery('SELECT id, columns FROM RadioTables ORDER BY id')
            ->fetchAllAssociative()
        ;

        $output->writeln('Fetched ' . count($radioTables) . ' radio tables.');

        $output->writeln('Fetching radio stations…');

        $radioStations = $this->connection
            ->executeQuery('SELECT id, rds_ps, rds_rt FROM RadioStations ORDER BY id')
            ->fetchAllAssociative()
        ;

        $output->writeln('Fetched ' . count($radioStations) . ' radio stations.');

        $this->connection->beginTransaction();

        foreach ($radioTables as ['id' => $id, 'columns' => $columns]) {
            $output->writeln('Handling radio table id = ' . $id);

            $this->connection->update(
                'RadioTables',
                ['columns' => $this->convert($columns)],
                ['id' => $id]
            );
        }

        foreach ($radioStations as ['id' => $id, 'rds_ps' => $ps, 'rds_rt' => $rt]) {
            $output->writeln('Handling radio station id = ' . $id);

            $this->connection->update(
                'RadioStations',
                ['rds_ps' => $this->convert($ps), 'rds_rt' => $this->convert($rt)],
                ['id' => $id]
            );
        }

        $output->writeln('Commiting database transaction…');

        $this->connection->commit();

        $output->writeln('Done.');

        return 0;
    }

    private function convert(string $data): string
    {
        return json_encode(unserialize($data), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
    }
}
