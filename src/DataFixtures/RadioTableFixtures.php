<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\Enum\RadioTable\Width;
use App\Entity\RadioTable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

class RadioTableFixtures extends AbstractEntityFixture implements DependentFixtureInterface
{
    protected const ENTITIES_NUMBER = 120;

    protected function createEntity(Generator $faker, int $i): object
    {
        $radioTable = new RadioTable;

        // Radio tables for hardcoded users.
        if ($i <= 6) {
            $radioTable->setName('Wykaz radiowy #' . $i);
            $radioTable->setDescription($faker->HTMLDescription);

            if ($i <= 3) {
                $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class, 1));
            }
            else {
                $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class, $i < 6 ? 2 : 3));
                $radioTable->setStatus(Status::PRIVATE);
            }

            $this->setPrivateFieldOfObject($radioTable, 'creationTime', $faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
            $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $faker->dateTimeBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

            return $radioTable;
        }

        $radioTable->setName($faker->words(rand(3, 8), true));
        $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class));
        $radioTable->setDescription($faker->optional()->HTMLDescription);
        if ($faker->boolean(25)) {
            $radioTable->setFrequencyUnit(FrequencyUnit::KHZ);
        }

        $this->setPrivateFieldOfObject($radioTable, 'creationTime', $faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $faker->dateTimeBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

        if ($faker->boolean(20)) {
            $this->setPrivateFieldOfObject($radioTable, 'creationTime', null);
        }

        if ($faker->boolean(40)) {
            $radioTable->setStatus($faker->randomEnum(Status::class));
        }
        $radioTable->setSorting($faker->randomElement(Column::getSortable()));
        $radioTable->setColumns($faker->columns());

        $appearance = $radioTable->getAppearance();
        $appearance->setWidthType($faker->randomEnum(Width::class));
        if ($faker->boolean) {
            $appearance->setBackgroundColor($faker->hexcolor);
            $appearance->setTextColor($faker->hexcolor);
        }
        $appearance->setCollapsedComments($faker->boolean);

        return $radioTable;
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
