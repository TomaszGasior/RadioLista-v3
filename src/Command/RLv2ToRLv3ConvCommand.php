<?php

// This script is dirty-written and will be removed in the future.

namespace App\Command;

use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RLv2ToRLv3ConvCommand extends Command
{
    protected static $defaultName = 'app:rlv2-to-rlv3-conv';

    private $radioTableRepository;
    private $radioStationRepository;
    private $entityManager;

    public function __construct(RadioTableRepository $radioTableRepository,
                                RadioStationRepository $radioStationRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->radioTableRepository = $radioTableRepository;
        $this->radioStationRepository = $radioStationRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Convert database contents from RLv2 to RLv3');

        $radioTables = $this->radioTableRepository->findAll();

        foreach ($radioTables as $radioTable) {
            // Appearance.

            if (count($radioTable->getAppearance()) < 5) {
                $missingKeys = [
                    'th'   => '',
                    'bg'   => '',
                    'fg'   => '',
                    'img'  => '',
                    'full' => false,
                ];
                $radioTable->setAppearance(
                    $radioTable->getAppearance() + $missingKeys
                );

                $io->text('RadioTable id=' . $radioTable->getId() . ' appearance, filled missing keys');
            }

            // Columns.

            $sortedVisibleColumns = array_filter(
                $radioTable->getColumns(),
                function($position){ return $position > 0; }
            );
            asort($sortedVisibleColumns);
            $sortedVisibleColumns = array_keys($sortedVisibleColumns);

            $radioTable->setColumns($sortedVisibleColumns);

            $io->text('RadioTable id=' . $radioTable->getId() . ' columns, only visible columns as value');



            // Unescape HTML.

            $columns = [
                'name',
            ];

            foreach ($columns as $columnName) {
                $currentValue = $radioTable->{'get' . $columnName}();
                $unescapedValue = is_array($currentValue)
                                  ? array_map('htmlspecialchars_decode', $currentValue)
                                  : htmlspecialchars_decode($currentValue);

                if ($currentValue !== $unescapedValue) {
                    $radioTable->{'set' . $columnName}($unescapedValue);

                    $io->text('RadioTable id=' . $radioTable->getId() . ' ' . $columnName . ', unescape HTML chars');
                }
            }
        }

        $radioStations = $this->radioStationRepository->findAll();

        foreach ($radioStations as $radioStation) {
            // Power.

            if ($radioStation->getPower() === '0.00') {
                $radioStation->setPower(null);

                $io->text('RadioStation id=' . $radioStation->getId() . ' power, change 0.00 to NULL');
            }

            // Private number.

            if ($radioStation->getPrivateNumber() === 0) {
                $radioStation->setPrivateNumber(null);

                $io->text('RadioStation id=' . $radioStation->getId() . ' privateNumber, change 0 to NULL');
            }

            // Unescape HTML.

            $columns = [
                'name',
                'radioGroup',
                'country',
                'location',
                'locality',
                'rds',
                'comment',
            ];

            foreach ($columns as $columnName) {
                $currentValue = $radioStation->{'get' . $columnName}();
                $unescapedValue = is_array($currentValue)
                                  ? array_map('htmlspecialchars_decode', $currentValue)
                                  : htmlspecialchars_decode($currentValue);

                if ($currentValue !== $unescapedValue) {
                    $radioStation->{'set' . $columnName}($unescapedValue);

                    $io->text('RadioStation id=' . $radioStation->getId() . ' ' . $columnName . ', unescape HTML chars');
                }
            }
        }

        $this->entityManager->flush();
        $io->text('Flushed EntityManager');
    }
}
