<?php

namespace App\DigitalMigration\Command;

use App\DigitalMigration\ConvertionDecision;
use App\DigitalMigration\ConvertionDecisionMaker;
use App\DigitalMigration\DataLossChecker;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:digital-migration:step-2:validate-radio-stations')]
class Step2ValidateRadioStationsCommand extends Command
{
    public function __construct(
        private RadioTableRepository $radioTableRepository,
        private RadioStationRepository $radioStationRepository,
        private ConvertionDecisionMaker $convertionDecisionMaker,
        private DataLossChecker $dataLossChecker,
    )
    {
        parent::__construct();
    }

    public function __invoke(OutputInterface $output): int
    {
        ini_set('memory_limit', '512M');

        $radioTables = $this->radioTableRepository->findAll();

        $radioTablesValidToConvertToMultiplexes = [];
        $radioTablesValidToConvertToDigitalRadioStations = [];

        $radioTablesToConvertToMultiplexesWithDataLoss = [];
        $radioTablesToConvertToDigitalRadioStationsWithDataLoss = [];

        $radioTablesWithMixedState = [];

        $dataLossPropertiesStatistics = [];

        foreach ($radioTables as $radioTable) {
            $output->write(sprintf('Handling RadioTable(id=%s): ', $radioTable->getId()));

            $radioStations = $this->radioStationRepository->findForRadioTable($radioTable);

            $digitalRadioStationsCount = 0;
            $multiplexesCount = 0;

            $radioStationsWithDataLoss = [];
            $dataLossProperties = [];

            foreach ($radioStations as $radioStation) {
                $convertionDecision = $this->convertionDecisionMaker->decide($radioStation);

                if (!$convertionDecision) {
                    continue;
                }

                if ($convertionDecision === ConvertionDecision::CONVERT_RADIO_STATION_TO_MULTIPLEX) {
                    ++$multiplexesCount;
                }
                elseif ($convertionDecision === ConvertionDecision::CONVERT_RADIO_STATION_TO_DIGITAL_RADIO_STATION) {
                    ++$digitalRadioStationsCount;
                }

                $dataLoss = $this->dataLossChecker->checkDataLoss($radioStation, $convertionDecision);

                if ($dataLoss) {
                    $radioStationsWithDataLoss[] = $radioStation;
                    $dataLossProperties = array_merge($dataLossProperties, $dataLoss);

                    $dataLossPropertiesStatistics = array_merge($dataLossPropertiesStatistics, $dataLoss);
                }
            }

            if ($digitalRadioStationsCount === 0 && $multiplexesCount === 0) {
                $output->writeln(' - No need for convertion.');
            }

            if ($digitalRadioStationsCount === 0 && $multiplexesCount > 0) {
                $output->writeln(' - Multiplexes only.');

                if ($radioStationsWithDataLoss) {
                    $radioTablesToConvertToMultiplexesWithDataLoss[] = $radioTable;
                }
                else {
                    $radioTablesValidToConvertToMultiplexes[] = $radioTable;
                }
            }

            if ($digitalRadioStationsCount > 0 && $multiplexesCount === 0) {
                $output->writeln(' - Digital radio stations only.');

                if ($radioStationsWithDataLoss) {
                    $radioTablesToConvertToDigitalRadioStationsWithDataLoss[] = $radioTable;
                }
                else {
                    $radioTablesValidToConvertToDigitalRadioStations[] = $radioTable;
                }
            }

            if ($digitalRadioStationsCount > 0 && $multiplexesCount > 0) {
                $output->writeln(' - MIXED STATE: both digital radio stations and empty multiplexes.');

                $radioTablesWithMixedState[] = $radioTable;
            }

            if ($radioStationsWithDataLoss) {
                $output->writeln([
                    ' - Radio station IDs with data loss: ' .  implode(
                        ', ',
                        array_map(
                            fn (RadioStation $radioStation) => $radioStation->getId(),
                            $radioStationsWithDataLoss,
                        ),
                    ),
                    ' - Radio station properties with data loss: ' .  implode(
                        ', ',
                        array_unique($dataLossProperties)
                    ),
                ]);
            }
        }

        $dataLossPropertiesStatistics = array_count_values($dataLossPropertiesStatistics);

        $output->writeln([
            '',
            'RadioTables valid to convert - multiplexes only (no data loss):',
            ...array_map(
                fn (RadioTable $radioTable) => sprintf(' - %s "%s"', $radioTable->getId(), $radioTable->getName()),
                $radioTablesValidToConvertToMultiplexes
            ),
            '',
            'RadioTables valid to convert - digital radio stations only (no data loss):',
            ...array_map(
                fn (RadioTable $radioTable) => sprintf(' - %s "%s"', $radioTable->getId(), $radioTable->getName()),
                $radioTablesValidToConvertToDigitalRadioStations
            ),
            '',
            'RadioTables with data loss - multiplexes only:',
            ...array_map(
                fn (RadioTable $radioTable) => sprintf(' - %s "%s"', $radioTable->getId(), $radioTable->getName()),
                $radioTablesToConvertToMultiplexesWithDataLoss
            ),
            '',
            'RadioTables with data loss - digital radio stations only:',
            ...array_map(
                fn (RadioTable $radioTable) => sprintf(' - %s "%s"', $radioTable->getId(), $radioTable->getName()),
                $radioTablesToConvertToDigitalRadioStationsWithDataLoss
            ),
            '',
            'RadioTables with mixed state (both digital radio stations and empty multiplexes):',
            ...array_map(
                fn (RadioTable $radioTable) => sprintf(' - %s "%s"', $radioTable->getId(), $radioTable->getName()),
                $radioTablesWithMixedState
            ),
            '',
            'Properties with data loss:',
            ...array_map(
                fn (string $property, int $count) => sprintf(' - %s (%d)', $property, $count),
                array_keys($dataLossPropertiesStatistics),
                array_values($dataLossPropertiesStatistics),
            ),
        ]);

        return 0;
    }
}
