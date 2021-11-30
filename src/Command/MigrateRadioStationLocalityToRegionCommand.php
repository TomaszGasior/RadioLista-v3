<?php

namespace App\Command;

use App\Entity\Embeddable\RadioStation\Locality;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @todo Remove this one-shot console command after migration.
 */
class MigrateRadioStationLocalityToRegionCommand extends Command
{
    protected static $defaultName = 'app:migrate-radio-station-locality-to-region';

    private $radioStationRepository;
    private $radioTableRepository;
    private $entityManager;

    public function __construct(RadioStationRepository $radioStationRepository,
                                RadioTableRepository $radioTableRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->radioStationRepository = $radioStationRepository;
        $this->radioTableRepository = $radioTableRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->migrateRadioStations($output);
        $this->migrateRadioTables($output);

        $this->entityManager->flush();

        return 0;
    }

    public function migrateRadioStations(OutputInterface $output): void
    {
        $radioStations = $this->radioStationRepository->findAll();

        foreach ($radioStations as $radioStation) {
            $output->write(sprintf('Radio station id=%d: ', $radioStation->getId()));

            $locality = $radioStation->getLocality();

            switch ($locality->getType()) {
                case Locality::TYPE_COUNTRY:
                    $output->writeln('skipped');
                    break;

                case Locality::TYPE_LOCAL:
                case Locality::TYPE_NETWORK:
                    $radioStation->setRegion($locality->getCity());

                    $output->writeln('migrated');
                    break;
            }
        }
    }

    public function migrateRadioTables(OutputInterface $output): void
    {
        $radioTables = $this->radioTableRepository->findAll();

        foreach ($radioTables as $radioTable) {
            $output->write(sprintf('Radio table id=%d: ', $radioTable->getId()));

            $columns = $radioTable->getColumns();
            $localityElementKey = array_search('locality', $columns, true);

            if (false === $localityElementKey) {
                $output->writeln('skipped');
            }
            else {
                $columns[$localityElementKey] = RadioTable::COLUMN_REGION;
                $radioTable->setColumns($columns);

                $output->writeln('migrated');
            }
        }
    }
}
