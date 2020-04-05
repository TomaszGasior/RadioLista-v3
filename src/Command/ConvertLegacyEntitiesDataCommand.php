<?php

namespace App\Command;

use App\Entity\Embeddable\RadioTable\Appearance;
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
 * @todo remove after 3.19 release
 */
class ConvertLegacyEntitiesDataCommand extends Command
{
    use ReflectionUtilsTrait;

    protected static $defaultName = 'app:convert-legacy-entities-data';

    private $entityManager;
    private $radioTableRepository;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,
                                RadioTableRepository $radioTableRepository,
                                ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->radioTableRepository = $radioTableRepository;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert database contents in 3.19 release')
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

            $appearance = $radioTable->getAppearance();
            $isFullWidth = $this->getPrivateFieldOfObject($appearance, 'legacyFullWidth');

            if ($isFullWidth) {
                $output->writeln('    is full width');
                $appearance->setWidthType(Appearance::WIDTH_FULL);
            }

            $this->validate($radioTable, $io);
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
}
