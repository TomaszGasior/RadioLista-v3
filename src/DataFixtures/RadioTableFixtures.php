<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\Enum\RadioTable\Width;
use App\Entity\RadioTable;
use App\Util\ReflectionUtilsTrait;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RadioTableFixtures extends AbstractEntityFixture implements DependentFixtureInterface
{
    use ReflectionUtilsTrait;

    protected const ENTITIES_NUMBER = 120;

    public function __construct(private Faker $faker) {}

    protected function createEntity(int $i): object
    {
        $radioTable = new RadioTable;

        // Radio tables for hardcoded users.
        if ($i <= 6) {
            $radioTable->setName('Wykaz radiowy #' . $i);
            $radioTable->setDescription($this->faker->HTMLDescription());

            if ($i <= 3) {
                $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class, 1));
            }
            else {
                $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class, $i < 6 ? 2 : 3));
                $radioTable->setStatus(Status::PRIVATE);
            }

            $this->setPrivateFieldOfObject($radioTable, 'creationTime', $this->faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
            $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $this->faker->dateTimeBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

            return $radioTable;
        }

        $radioTable->setName($this->faker->words(rand(3, 8), true));
        $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class));
        $radioTable->setDescription($this->faker->optional()->HTMLDescription());
        if ($this->faker->boolean(25)) {
            $radioTable->setFrequencyUnit(FrequencyUnit::KHZ);
        }

        $this->setPrivateFieldOfObject($radioTable, 'creationTime', $this->faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $this->faker->dateTimeBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

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
        if ($this->faker->boolean()) {
            $appearance->setBackgroundColor($this->faker->hexcolor());
            $appearance->setTextColor($this->faker->hexcolor());
        }
        $appearance->setCollapsedComments($this->faker->boolean());

        return $radioTable;
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
