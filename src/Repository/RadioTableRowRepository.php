<?php

namespace App\Repository;

use App\Entity\DigitalRadioStation;
use App\Entity\Enum\RadioTable\DigitalType;
use App\Entity\Multiplex;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Row\RadioTableRowFactory;
use App\Util\RadioTableRowSorter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RadioTableRowRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RadioTableRowFactory $radioTableRowFactory,
        private RadioTableRowSorter $radioTableRowSorter,
    ) {}

    public function findForRadioTable(RadioTable $radioTable): array
    {
        $rows = array_map(
            fn (object $object) => $this->radioTableRowFactory->create($object),
            match ($radioTable->getDigitalType()) {
                DigitalType::DISABLED =>
                    $this->findRadioStations($radioTable),
                DigitalType::RADIO_STATIONS_SEPARATELY =>
                    $this->findRadioStations($radioTable) + $this->findDigitalRadioStations($radioTable),
                DigitalType::MULTIPLEXES_MERGED =>
                    $this->findRadioStations($radioTable) + $this->findMultiplexes($radioTable),
            }
        );

        $this->radioTableRowSorter->sort($rows, $radioTable->getSorting());

        return $rows;
    }

    /**
     * @return RadioStation[]
     */
    private function findRadioStations(RadioTable $radioTable): array
    {
        return $this->createQueryBuilder(RadioStation::class, 'radioStation')
            ->andWhere('radioStation.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return DigitalRadioStation[]
     */
    private function findDigitalRadioStations(RadioTable $radioTable): array
    {
        return $this->createQueryBuilder(DigitalRadioStation::class, 'digitalRadioStation')
            ->innerJoin('digitalRadioStation.multiplex', 'multiplex')
            ->andWhere('multiplex.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Multiplex[]
     */
    private function findMultiplexes(RadioTable $radioTable): array
    {
        return $this->createQueryBuilder(Multiplex::class, 'multiplex')
            ->andWhere('multiplex.radioTable = :radioTable')
            ->setParameter('radioTable', $radioTable)
            ->getQuery()
            ->getResult()
        ;
    }

    private function createQueryBuilder(string $className, string $alias): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select($alias)
            ->from($className, $alias)
        ;
    }
}
