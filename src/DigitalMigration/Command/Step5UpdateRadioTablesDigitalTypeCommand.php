<?php

namespace App\DigitalMigration\Command;

use App\Entity\Enum\RadioTable\DigitalType;
use App\Entity\RadioTable;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:digital-migration:step-5:update-radio-tables-digital-type')]
class Step5UpdateRadioTablesDigitalTypeCommand extends Command
{
    public function __construct(
        private RadioTableRepository $radioTableRepository,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Option] bool $persistChanges = false,
    ): int
    {
        ini_set('memory_limit', '512M');

        $radioTables = $this->radioTableRepository->findAll();
        $modifiedRadioTables = [];

        foreach ($radioTables as $radioTable) {
            $output->writeln(sprintf('Handling RadioTable(id=%s).', $radioTable->getId()));

            if ($radioTable->getMultiplexesCount() > 0) {
                if ($radioTable->getDigitalRadioStationsCount() > 0) {
                    $radioTable->setDigitalType(DigitalType::RADIO_STATIONS_SEPARATELY);

                    $output->writeln(' - Digital type set to RADIO_STATIONS_SEPARATELY.');
                }
                else {
                    $radioTable->setDigitalType(DigitalType::MULTIPLEXES_MERGED);

                    $output->writeln(' - Digital type set to MULTIPLEXES_MERGED.');
                }

                $modifiedRadioTables[$radioTable->getId()] = $radioTable;
            }
        }

        $output->writeln([
            '',
            'Modified RadioTables:',
            ...array_map(
                fn (RadioTable $radioTable) => sprintf(' - %s "%s"', $radioTable->getId(), $radioTable->getName()),
                $modifiedRadioTables
            ),
            '',
        ]);

        if ($persistChanges) {
            $output->writeln('Flushing into database…');
            $this->entityManager->flush();
            $output->writeln('Flushed.');
        }

        return 0;
    }
}
