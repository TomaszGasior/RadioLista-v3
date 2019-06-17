<?php

namespace App\Command;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoveRdsPiToSeparateColumnCommand extends Command
{
    private $entityManager;
    private $radioStationRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                RadioStationRepository $radioStationRepository)
    {
        $this->entityManager = $entityManager;
        $this->radioStationRepository = $radioStationRepository;

        parent::__construct();
    }

    protected static $defaultName = 'app:move-rds-pi-to-separate-column';

    protected function configure()
    {
        $this
            ->setHidden(true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager->getClassMetadata(User::class)->entityListeners = [];
        $this->entityManager->getClassMetadata(RadioTable::class)->entityListeners = [];
        $this->entityManager->getClassMetadata(RadioStation::class)->entityListeners = [];

        $radioStations = $this->radioStationRepository->findAll();

        foreach ($radioStations as $radioStation) {
            $rds = $radioStation->getRds();

            if ('' !== $rds['pi']) {
                $radioStation->setRdsPi($rds['pi']);
            }

            unset($rds['pi']);
            $radioStation->setRds($rds);

            $this->entityManager->persist($radioStation);
        }

        $this->entityManager->flush();
    }
}
