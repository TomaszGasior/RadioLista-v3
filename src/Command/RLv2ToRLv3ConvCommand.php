<?php

// This script is dirty-written and will be removed in the future.

namespace App\Command;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RLv2ToRLv3ConvCommand extends Command
{
    protected static $defaultName = 'app:rlv2-to-rlv3-conv';

    private $radioTableRepository;
    private $radioStationRepository;
    private $entityManager;
    private $userRepository;
    private $validator;

    public function __construct(RadioTableRepository $radioTableRepository,
                                RadioStationRepository $radioStationRepository,
                                UserRepository $userRepository,
                                EntityManagerInterface $entityManager,
                                ValidatorInterface $validator)
    {
        parent::__construct();

        $this->radioTableRepository = $radioTableRepository;
        $this->radioStationRepository = $radioStationRepository;
        $this->userRepository = $userRepository;

        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addOption('validate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $input->getOption('validate') ? $this->validate($io) : $this->convert($io);
    }

    private function convert(SymfonyStyle $io): void
    {
        // Disable lifecycle events.
        $this->entityManager->getClassMetadata(User::class)->entityListeners = [];
        $this->entityManager->getClassMetadata(RadioTable::class)->entityListeners = [];
        $this->entityManager->getClassMetadata(RadioStation::class)->entityListeners = [];

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

            $appearance = $radioTable->getAppearance();
            if (false == is_bool($appearance['full'])) {
                $appearance['full'] = (bool)$appearance['full'];
                $radioTable->setAppearance($appearance);

                $io->text('RadioTable id=' . $radioTable->getId() . ' appearance, convert full to bool');
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

            // Marker.

            if ($radioStation->getMarker() === 0) {
                $radioStation->setMarker(null);

                $io->text('RadioStation id=' . $radioStation->getId() . ' marker, change 0 to NULL');
            }

            // Polarization.

            if ($radioStation->getPolarization() === '0' || $radioStation->getPolarization() === '') {
                $radioStation->setPolarization(null);

                $io->text('RadioStation id=' . $radioStation->getId() . ' polarization, change "0" to NULL');
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

            // Locality.

            $locality = $radioStation->getLocality();
            if (false === is_int($locality['type'])) {
                $locality['type'] = (int)$locality['type'];
                $radioStation->setLocality($locality);

                $io->text('RadioStation id=' . $radioStation->getId() . ' locality, convert type to int');
            }
        }

        $this->entityManager->flush();
        $io->text('Flushed EntityManager');
    }

    private function validate(SymfonyStyle $io): void
    {
        $io->title('Validate all entities');

        // Users.

        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $errors = $this->validator->validate($user);

            if (count($errors) > 0) {
                $io->text('User id=' . $user->getId());
                $io->text((string) $errors);
            }
        }

        // Radiotables.

        $radioTables = $this->radioTableRepository->findAll();

        foreach ($radioTables as $radioTable) {
            $errors = $this->validator->validate($radioTable);

            if (count($errors) > 0) {
                $io->text('RadioTable id=' . $radioTable->getId());
                $io->text((string) $errors);
            }
        }

        // Radiostations.

        $radioStations = $this->radioStationRepository->findAll();

        foreach ($radioStations as $radioStation) {
            $errors = $this->validator->validate($radioStation);

            if (count($errors) > 0) {
                $io->text('RadioStation id=' . $radioStation->getId());
                $io->text((string) $errors);
            }
        }
    }
}
