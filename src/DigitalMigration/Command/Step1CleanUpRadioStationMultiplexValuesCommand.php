<?php

namespace App\DigitalMigration\Command;

use App\Entity\Enum\RadioTable\Column;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:digital-migration:step-1:clean-up-radio-station-multiplex-values')]
class Step1CleanUpRadioStationMultiplexValuesCommand extends Command
{
    public function __construct(
        private Connection $database,
        private RadioTableRepository $radioTableRepository,
        private RadioStationRepository $radioStationRepository,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument] string $radioTableIds = '',
        #[Option] bool $persistChanges = false,
    ): int
    {
        ini_set('memory_limit', '512M');

        if (!$radioTableIds) {
            $result = $this->database->fetchAllAssociative(
                <<<SQL
                    SELECT
                        rs.radioTableId AS 'radio table id',
                        GROUP_CONCAT(DISTINCT rs.multiplex) AS 'multiplex values',
                        GROUP_CONCAT(rs.id) AS 'radio station ids'
                    FROM RadioStations rs
                    WHERE 1=1
                        AND rs.multiplex NOT LIKE '%mux%'
                        AND rs.multiplex NOT LIKE '%dab%'
                        AND rs.multiplex NOT LIKE '%multiplex%'
                    GROUP BY rs.radioTableId
                SQL
            );

            $output->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return 0;
        }

        $radioTables = $this->radioTableRepository->findBy(['id' => explode(',', $radioTableIds)]);

        foreach ($radioTables as $radioTable) {
            $output->writeln(sprintf('Handling RadioTable(id=%s).', $radioTable->getId()));

            $radioStations = $this->radioStationRepository->findForRadioTable($radioTable);

            foreach ($radioStations as $radioStation) {
                $comment = $radioStation->getComment();
                $multiplex = $radioStation->getMultiplex();

                if (null === $multiplex) {
                    continue;
                }

                if (null !== $comment) {
                    $comment .= "\n\n" . $multiplex;
                }
                else {
                    $comment = $multiplex;
                }

                $radioStation->setComment($comment);
                $radioStation->setMultiplexToNull();

                $output->writeln(sprintf(' - Handling RadioStation(id=%s).', $radioStation->getId()));
            }

            $columns = $radioTable->getColumns();

            if (in_array(Column::MULTIPLEX, $columns)) {
                if (!in_array(Column::COMMENT, $columns)) {
                    $columns = array_map(
                        function (Column $column) {
                            if ($column === Column::MULTIPLEX) {
                                return Column::COMMENT;
                            }

                            return $column;
                        },
                        $columns,
                    );
                }
                else {
                    $columns = array_filter(
                        $columns,
                        fn (Column $column) => $column !== Column::MULTIPLEX,
                    );
                }

                $radioTable->setColumns($columns);
            }
        }

        if ($persistChanges) {
            $output->writeln('Flushing into database…');
            $this->entityManager->flush();
            $output->writeln('Flushed.');
        }

        return 0;
    }
}
