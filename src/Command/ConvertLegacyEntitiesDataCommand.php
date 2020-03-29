<?php

namespace App\Command;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Repository\RadioStationRepository;
use App\Repository\RadioTableRepository;
use App\Util\ReflectionUtilsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @todo remove after 3.17 release
 */
class ConvertLegacyEntitiesDataCommand extends Command
{
    use ReflectionUtilsTrait;

    protected static $defaultName = 'app:convert-legacy-entities-data';

    private $entityManager;
    private $radioTableRepository;
    private $radioStationRepository;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,
                                RadioTableRepository $radioTableRepository,
                                RadioStationRepository $radioStationRepository,
                                ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->radioTableRepository = $radioTableRepository;
        $this->radioStationRepository = $radioStationRepository;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert database contents in 3.17 release')
            ->setHidden(true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->getClassMetadata(User::class)->entityListeners = [];
        $this->entityManager->getClassMetadata(RadioTable::class)->entityListeners = [];
        $this->entityManager->getClassMetadata(RadioStation::class)->entityListeners = [];

        $io = new SymfonyStyle($input, $output);

        foreach ($this->radioTableRepository->findAll() as $radioTable) {
            $output->writeln('RadioTable id=' . $radioTable->getId());

            $this->radioTableAppearance($radioTable);
            $this->radioTableFrequencyUnit($radioTable);

            $this->validate($radioTable, $io);
        }

        foreach ($this->radioStationRepository->findAll() as $radioStation) {
            $output->writeln('RadioStation id=' . $radioStation->getId());

            $this->radioStationLocality($radioStation);
            $this->radioStationRds($radioStation);

            $this->validate($radioStation, $io);
        }

        $this->entityManager->flush();

        return 0;
    }

    private function validate(object $entity, SymfonyStyle $io): void
    {
        $errors = $this->validator->validate($entity);

        if ($errors->count() > 0) {
            $io->error((string) $errors);
        }
    }

    private function radioTableAppearance(RadioTable $radioTable): void
    {
        $data = $this->getPrivateFieldOfObject($radioTable, 'legacyAppearance');

        ($radioTable->getAppearance())
            ->setTheme($data['th'])
            ->setTextColor($data['fg'])
            ->setBackgroundColor($data['bg'])
            ->setBackgroundImage($data['img'])
            ->setFullWidth($data['full'])
        ;
    }

    private function radioTableFrequencyUnit(RadioTable $radioTable): void
    {
        $useKhz = $this->getPrivateFieldOfObject($radioTable, 'useKhz');

        $radioTable->setFrequencyUnit(
            $useKhz ? RadioTable::FREQUENCY_KHZ : RadioTable::FREQUENCY_MHZ
        );
    }

    private function radioStationLocality(RadioStation $radioStation): void
    {
        $data = $this->getPrivateFieldOfObject($radioStation, 'legacyLocality');

        ($radioStation->getLocality())
            ->setCity($data['city'])
            ->setType($data['type'])
        ;
    }

    private function radioStationRds(RadioStation $radioStation): void
    {
        $rds = $this->getPrivateFieldOfObject($radioStation, 'legacyRds');
        $rdsPi = $this->getPrivateFieldOfObject($radioStation, 'legacyRdsPi');

        ($radioStation->getRds())
            ->setPs($rds['ps'])
            ->setRt($rds['rt'])
            ->setPty($rds['pty'])
            ->setPi($rdsPi)
        ;
    }
}
