<?php

namespace App\Command;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Form\DataTransformer\RadioStationRdsPsFrameTransformer;
use App\Form\DataTransformer\RadioStationRdsRtFrameTransformer;
use App\Repository\RadioStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransformRdsColumnCommand extends Command
{
    private $entityManager;
    private $radioStationRepository;
    private $rdsPsTransformer;
    private $rdsRtTransformer;

    public function __construct(EntityManagerInterface $entityManager,
                                RadioStationRepository $radioStationRepository,
                                RadioStationRdsPsFrameTransformer $rdsPsTransformer,
                                RadioStationRdsRtFrameTransformer $rdsRtTransformer)
    {
        $this->entityManager = $entityManager;
        $this->radioStationRepository = $radioStationRepository;

        $this->rdsPsTransformer = $rdsPsTransformer;
        $this->rdsRtTransformer = $rdsRtTransformer;

        parent::__construct();
    }

    protected static $defaultName = 'app:transform-rds-column';

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

            $rds['ps'] = $this->rdsPsTransformer->reverseTransform($rds['ps']);
            $rds['rt'] = $this->rdsRtTransformer->reverseTransform($rds['rt']);

            $radioStation->setRds($rds);
            $this->entityManager->persist($radioStation);
        }

        $this->entityManager->flush();
    }
}
