<?php

namespace App\Command;

use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\RadioStation;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo Remove this one-shot console command after migration.
 */
class MigrateRadioStationMarkersToAppearanceCommand extends Command
{
    protected static $defaultName = 'app:migrate-radio-station-markers-to-appearance';

    private $radioStationRepository;
    private $entityManager;

    public function __construct(RadioStationRepository $radioStationRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->radioStationRepository = $radioStationRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $radioStations = $this->radioStationRepository->findAll();

        foreach ($radioStations as $radioStation) {
            $output->write(sprintf('Radio station id=%d: ', $radioStation->getId()));

            $appearance = $radioStation->getAppearance();
            $migratedValue = true;

            switch ($radioStation->getMarker()) {
                case RadioStation::MARKER_1:
                    $appearance->setBold(true);
                    break;

                case RadioStation::MARKER_2:
                    $appearance->setItalic(true);
                    break;

                case RadioStation::MARKER_3:
                    $appearance->setStrikethrough(true);
                    break;

                case RadioStation::MARKER_4:
                    $appearance->setBackground(Appearance::BACKGROUND_RED);
                    break;

                case RadioStation::MARKER_5:
                    $appearance->setBackground(Appearance::BACKGROUND_GREEN);
                    break;

                case RadioStation::MARKER_6:
                    $appearance->setBackground(Appearance::BACKGROUND_BLUE);
                    break;

                default:
                    $migratedValue = false;
                    break;
            }

            $output->writeln($migratedValue ? 'migrated' : 'skipped');
        }

        $this->entityManager->flush();

        return 0;
    }
}
