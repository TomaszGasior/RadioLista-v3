<?php

namespace App\Command;

use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddRdsPiFieldCommand extends Command
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

    protected static $defaultName = 'app:add-rds-pi-field';

    protected function configure()
    {
        $this
            ->setHidden(true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $radioStations = $this->radioStationRepository->findAll();

        foreach ($radioStations as $radioStation) {
            $rds = $radioStation->getRds();
            $rds['pi'] = '';
            $radioStation->setRds($rds);

            $this->entityManager->persist($radioStation);
        }

        $this->entityManager->flush();
    }
}
