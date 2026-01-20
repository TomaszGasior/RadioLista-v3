<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\MaxSignalLevelUnit;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\Enum\RadioTable\Width;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Util\ReflectionUtilsTrait;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('dev')]
class RadioTableFixtures extends Fixture implements DependentFixtureInterface
{
    use ReflectionUtilsTrait;

    public function __construct(
        #[Autowire('%kernel.project_dir%/fixtures/radio_table.php')] private string $source,
    ) {}

    public function load(ObjectManager $manager): void
    {
        foreach (include $this->source as $id => $data) {
            [$name, $status, $columns, $sorting, $description, $lastUpdateTime, $creationTime, $ownerId, $frequencyUnit, $maxSignalLevelUnit, $appearanceWidthType, $appearanceCustomWidth, $appearanceCollapsedComments] = $data;

            $owner = $this->getReference(User::class . $ownerId, User::class);

            $radioTable = (new RadioTable($name, $owner))
                ->setStatus(Status::from($status))
                ->setColumns(array_map(fn(string $column) => Column::from($column), $columns))
                ->setSorting(Column::from($sorting))
                ->setDescription($description)
                ->setFrequencyUnit(FrequencyUnit::from($frequencyUnit))
                ->setMaxSignalLevelUnit(MaxSignalLevelUnit::from($maxSignalLevelUnit))
            ;

            $radioTable->getAppearance()
                ->setWidthType(Width::from($appearanceWidthType))
                ->setCustomWidth($appearanceCustomWidth)
                ->setCollapsedComments($appearanceCollapsedComments)
            ;

            $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', new DateTimeImmutable($lastUpdateTime));
            $this->setPrivateFieldOfObject($radioTable, 'creationTime', $creationTime ? new DateTimeImmutable($creationTime) : $creationTime);

            $this->addReference(RadioTable::class . $id, $radioTable);
            $manager->persist($radioTable);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
