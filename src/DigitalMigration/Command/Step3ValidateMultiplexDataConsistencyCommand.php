<?php

namespace App\DigitalMigration\Command;

use App\DigitalMigration\ConvertionDecision;
use App\DigitalMigration\ConvertionDecisionMaker;
use App\DigitalMigration\ConvertionPropertiesMapping;
use App\DigitalMigration\DataLossChecker;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:digital-migration:step-3:validate-multiplex-data-consistency')]
class Step3ValidateMultiplexDataConsistencyCommand extends Command
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

    public function __invoke(OutputInterface $output, #[Argument] string $radioTableIds): int
    {
        ini_set('memory_limit', '512M');

        $radioTables = $this->radioTableRepository->findBy(['id' => explode(',', $radioTableIds)]);

        foreach ($radioTables as $radioTable) {
            $output->writeln(sprintf('Handling RadioTable(id=%s).', $radioTable->getId()));

            $radioStations = $this->radioStationRepository->findForRadioTable($radioTable);

            $multiplexNameToData = [];

            foreach ($radioStations as $radioStation) {
                $convertionDecision = $this->convertionDecisionMaker->decide($radioStation);

                if ($convertionDecision !== ConvertionDecision::CONVERT_RADIO_STATION_TO_DIGITAL_RADIO_STATION) {
                    continue;
                }

                $multiplexName = mb_strtolower($radioStation->getMultiplex());
                $multiplexData = $multiplexNameToData[$multiplexName] ?? null;

                if ($multiplexData === null) {
                    $multiplexNameToData[$multiplexName] = $this->getMultiplexDataFromRadioStation($radioStation);

                    continue;
                }

                $inconsistentProperties = $this->validateMultiplexDataInRadioStation($radioStation, $multiplexData);

                if ($inconsistentProperties) {
                    $output->writeln(sprintf(
                        '- INCONSISTENT DATA in RadioStation(id=%s) with multiplex "%s": %s.',
                        $radioStation->getId(),
                        $radioStation->getMultiplex(),
                        implode(', ', $inconsistentProperties),
                    ));
                }
            }
        }

        return 0;
    }

    private function getMultiplexDataFromRadioStation(RadioStation $radioStation): array
    {
        $multiplexData = [];

        foreach (ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_MULTIPLEX_THROUGH_DIGITAL_RADIO_STATION_DEPENDENCY as $propertyName) {
            $multiplexData[$propertyName] = $radioStation->{'get' . $propertyName}();
        }

        return $multiplexData;
    }

    /**
     * @return array Property names with inconsistent data.
     */
    private function validateMultiplexDataInRadioStation(RadioStation $radioStation, array $expectedMultiplexData): array
    {
        $multiplexData = $this->getMultiplexDataFromRadioStation($radioStation);

        $diff = array_filter(
            $expectedMultiplexData,
            fn (mixed $expectedValue, string $key) => $multiplexData[$key] !== $expectedValue,
            ARRAY_FILTER_USE_BOTH
        );

        return array_keys($diff);
    }
}
