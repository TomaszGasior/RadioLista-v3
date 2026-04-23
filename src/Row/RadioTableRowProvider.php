<?php

namespace App\Row;

use App\Entity\DigitalRadioStation;
use App\Entity\Enum\RadioTable\DigitalType;
use App\Entity\Multiplex;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Model\Row;
use App\Repository\DigitalRadioStationRepository;
use App\Repository\MultiplexRepository;
use App\Repository\RadioStationRepository;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class RadioTableRowProvider
{
    public function __construct(
        #[AutowireIterator(RowFactoryInterface::class)] private iterable $rowFactories,
        private RadioStationRepository $radioStationRepository,
        private DigitalRadioStationRepository $digitalRadioStationRepository,
        private MultiplexRepository $multiplexRepository,
        private RadioTableRowSorter $radioTableRowSorter,
    ) {}

    public function findForRadioTable(RadioTable $radioTable): array
    {
        $rows = array_map(
            fn (RadioStation|DigitalRadioStation|Multiplex $object) => $this->createRow($object),
            match ($radioTable->getDigitalType()) {
                DigitalType::DISABLED => $this->radioStationRepository->findForRadioTable($radioTable),
                DigitalType::RADIO_STATIONS_SEPARATELY => array_merge(
                    $this->radioStationRepository->findForRadioTable($radioTable),
                    $this->digitalRadioStationRepository->findForRadioTable($radioTable),
                ),
                DigitalType::MULTIPLEXES_MERGED => array_merge(
                    $this->radioStationRepository->findForRadioTable($radioTable),
                    $this->multiplexRepository->findForRadioTable($radioTable),
                ),
            }
        );

        $this->radioTableRowSorter->sort($rows, $radioTable->getSorting());

        return $rows;
    }

    public function countForRadioTable(RadioTable $radioTable): int
    {
        return match ($radioTable->getDigitalType()) {
            DigitalType::DISABLED => $radioTable->getRadioStationsCount(),
            DigitalType::RADIO_STATIONS_SEPARATELY => $radioTable->getRadioStationsCount() + $radioTable->getDigitalRadioStationsCount(),
            DigitalType::MULTIPLEXES_MERGED => $this->radioStationRepository->findForRadioTable($radioTable) + $radioTable->getMultiplexesCount(),
        };
    }

    private function createRow(RadioStation|DigitalRadioStation|Multiplex $object): Row
    {
        foreach ($this->rowFactories as $factory) {
            if ($factory->supports($object)) {
                return $factory->create($object);
            }
        }

        throw new RuntimeException(sprintf('Object "%s" is not supported.', $object::class));
    }
}
