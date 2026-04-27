<?php

namespace App\DigitalMigration\Command;

use App\DigitalMigration\ConvertionDecision;
use App\DigitalMigration\ConvertionDecisionMaker;
use App\DigitalMigration\ConvertionPropertiesMapping;
use App\Entity\DigitalRadioStation;
use App\Entity\Multiplex;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand('app:digital-migration:step-4:convert-radio-stations-to-digital-multiplexes')]
class Step4ConvertRadioStationsToDigitalMultiplexesCommand extends Command
{
    private OutputInterface $output;

    private int $removedRadioStationsCount = 0;
    private int $createdMultiplexesCount = 0;
    private int $createdDigitalRadioStationsCount = 0;

    public function __construct(
        private RadioTableRepository $radioTableRepository,
        private RadioStationRepository $radioStationRepository,
        private EntityManagerInterface $entityManager,
        private ConvertionDecisionMaker $convertionDecisionMaker,
        private ValidatorInterface $validator,
    )
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument] string $radioTableIds,
        #[Option] bool $persistChanges = false,
    ): int
    {
        ini_set('memory_limit', '512M');

        $this->output = $output;

        $radioTables = $this->radioTableRepository->findBy(['id' => explode(',', $radioTableIds)]);
        $modifiedRadioTables = [];

        foreach ($radioTables as $radioTable) {
            $this->output->writeln(sprintf('Handling RadioTable(id=%s).', $radioTable->getId()));

            $radioStations = $this->radioStationRepository->findForRadioTable($radioTable);

            $radioTableMultiplexes = [];

            foreach ($radioStations as $radioStation) {
                $convertionDecision = $this->convertionDecisionMaker->decide($radioStation);

                if ($convertionDecision === ConvertionDecision::CONVERT_RADIO_STATION_TO_MULTIPLEX) {
                    $this->convertRadioStationToMultiplex($radioStation);

                    $modifiedRadioTables[$radioTable->getId()] = $radioTable;
                }
                elseif ($convertionDecision === ConvertionDecision::CONVERT_RADIO_STATION_TO_DIGITAL_RADIO_STATION) {
                    $this->convertRadioStationToDigitalRadioStationAndMultiplex($radioStation, $radioTableMultiplexes);

                    $modifiedRadioTables[$radioTable->getId()] = $radioTable;
                }
            }
        }

        $this->output->writeln('Removed RadioStations: ' . $this->removedRadioStationsCount);
        $this->output->writeln('Created Multiplexes: ' . $this->createdMultiplexesCount);
        $this->output->writeln('Created DigitalRadioStations: ' . $this->createdDigitalRadioStationsCount);

        $this->output->writeln([
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

    private function convertRadioStationToMultiplex(RadioStation $radioStation): void
    {
        $this->output->writeln(sprintf(' - Removing RadioStation(id=%s), creating Multiplex.', $radioStation->getId()));

        $multiplex = new Multiplex(
            frequency: '',
            name: '',
            radioTable: $radioStation->getRadioTable(),
        );

        foreach (ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_MULTIPLEX_DIRECTLY as $propertyName) {
            $multiplex->{'set' . $propertyName}($radioStation->{'get' . $propertyName}());
        }

        if (
            $radioStation->getMultiplex() &&
            !str_contains(mb_strtolower($multiplex->getName()), mb_strtolower($radioStation->getMultiplex()))
        ) {
            $multiplex->setName(
                $multiplex->getName() . ' – ' . $radioStation->getMultiplex()
            );
        }

        $this->validate($multiplex);

        $this->entityManager->persist($multiplex);
        $this->entityManager->remove($radioStation);

        ++$this->createdMultiplexesCount;
        ++$this->removedRadioStationsCount;
    }

    /**
     * @param array<string, Multiplex> $radioTableMultiplexes
     */
    private function convertRadioStationToDigitalRadioStationAndMultiplex(RadioStation $radioStation, array &$radioTableMultiplexes): void
    {
        $this->output->writeln(sprintf(' - Removing RadioStation(id=%s), creating DigitalRadioStation.', $radioStation->getId()));

        $multiplex = $radioTableMultiplexes[mb_strtolower($radioStation->getMultiplex())] ?? null;

        if (null === $multiplex) {
            $this->output->writeln(sprintf(' - Creating Multiplex(name="%s")', $radioStation->getMultiplex()));

            $multiplex = new Multiplex(
                frequency: '',
                name: $radioStation->getMultiplex(),
                radioTable: $radioStation->getRadioTable(),
            );

            foreach (ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_MULTIPLEX_THROUGH_DIGITAL_RADIO_STATION_DEPENDENCY as $propertyName) {
                $multiplex->{'set' . $propertyName}($radioStation->{'get' . $propertyName}());
            }

            $this->validate($multiplex);

            $radioTableMultiplexes[mb_strtolower($radioStation->getMultiplex())] = $multiplex;
            $this->entityManager->persist($multiplex);

            ++$this->createdMultiplexesCount;
        }

        $digitalRadioStation = new DigitalRadioStation(
            name: '',
            multiplex: $multiplex,
        );

        foreach (ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_DIGITAL_RADIO_STATION as $propertyName) {
            $digitalRadioStation->{'set' . $propertyName}($radioStation->{'get' . $propertyName}());
        }

        $this->validate($digitalRadioStation);

        $this->entityManager->persist($digitalRadioStation);
        $this->entityManager->remove($radioStation);

        ++$this->createdDigitalRadioStationsCount;
        ++$this->removedRadioStationsCount;
    }

    private function validate(DigitalRadioStation|Multiplex $object): void
    {
        $errors = $this->validator->validate($object);

        if ($errors->count() > 0) {
            $this->output->writeln([
                sprintf(' - Validation errors for %s(id=%s):', get_class($object), $object->getId()),
                (string) $errors,
            ]);
        }
    }
}
