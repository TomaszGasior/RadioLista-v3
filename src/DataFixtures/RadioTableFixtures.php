<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\Enum\RadioTable\Width;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Util\ReflectionUtilsTrait;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RadioTableFixtures extends AbstractEntityFixture implements DependentFixtureInterface
{
    use ReflectionUtilsTrait;

    protected const ENTITIES_NUMBER = 180;

    public function __construct(private Faker $faker) {}

    protected function createEntity(int $i): object
    {
        $radioTable = new RadioTable(
            name: match ($i) {
                1, 2, 3, 4, 5 => 'Wykaz radiowy #' . $i,
                default => $this->faker->words(rand(3, 8), true),
            },
            owner: match ($i) {
                1, 2, 3, 4, 5 => $this->getEntity(User::class, 1),
                default => $this->getEntity(User::class),
            },
        );

        $radioTable->setDescription($this->faker->optional()->HTMLDescription());
        if ($this->faker->boolean(25)) {
            $radioTable->setFrequencyUnit(FrequencyUnit::KHZ);
        }

        $this->setPrivateFieldOfObject($radioTable, 'creationTime', $this->faker->dateTimeImmutableBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $this->faker->dateTimeImmutableBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

        if ($this->faker->boolean(20)) {
            $this->setPrivateFieldOfObject($radioTable, 'creationTime', null);
        }

        if ($this->faker->boolean(40)) {
            $radioTable->setStatus($this->faker->randomEnum(Status::class));
        }
        $radioTable->setSorting($this->faker->randomElement(Column::getSortable()));

        if ($this->faker->boolean()) {
            $radioTable->setColumns($this->faker->columns());
        }

        $appearance = $radioTable->getAppearance();
        $appearance->setWidthType($this->faker->randomEnum(Width::class));
        if (Width::CUSTOM === $appearance->getWidthType()) {
            $appearance->setCustomWidth($this->faker->numberBetween(900, 2000));
        }
        $appearance->setCollapsedComments($this->faker->boolean());

        return $radioTable;
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
