<?php

namespace App\DataFixtures;

use App\Entity\Embeddable\RadioTable\Appearance;
use App\Entity\RadioTable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

class RadioTableFixtures extends AbstractEntityFixture implements DependentFixtureInterface
{
    protected const ENTITIES_NUMBER = 120;

    protected function createEntity(Generator $faker, int $i): object
    {
        $radioTable = new RadioTable;

        // Radiotables for hardcoded users.
        if ($i <= 6) {
            $radioTable->setName('Wykaz radiowy #' . $i);
            $radioTable->setDescription($faker->HTMLDescription);

            if ($i <= 3) {
                $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class, 1));
            }
            else {
                $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class, $i < 6 ? 2 : 3));
                $radioTable->setStatus(RadioTable::STATUS_PRIVATE);
            }

            $this->setPrivateFieldOfObject($radioTable, 'creationTime', $faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
            $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $faker->dateTimeBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

            return $radioTable;
        }

        $radioTable->setName($faker->words(rand(3, 8), true));
        $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class));
        $radioTable->setDescription($faker->optional()->HTMLDescription);
        if ($faker->boolean(25)) {
            $radioTable->setFrequencyUnit(RadioTable::FREQUENCY_KHZ);
        }

        $this->setPrivateFieldOfObject($radioTable, 'creationTime', $faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $faker->dateTimeBetween($radioTable->getCreationTime(), $radioTable->getOwner()->getLastActivityDate()));

        if ($faker->boolean(20)) {
            $this->setPrivateFieldOfObject($radioTable, 'creationTime', null);
        }

        if ($faker->boolean(40)) {
            $radioTable->setStatus(
                $faker->randomConstantFromClass(RadioTable::class, 'STATUS_')
            );
        }
        $radioTable->setSorting($faker->randomElement([
            $faker->randomConstantFromClass(RadioTable::class, 'SORTING_')
        ]));
        $radioTable->setColumns(
            array_unique(array_merge(
                [RadioTable::COLUMN_FREQUENCY, RadioTable::COLUMN_NAME],
                $faker->randomConstantsFromClass(RadioTable::class, 'COLUMN_')
            ))
        );

        $appearance = $radioTable->getAppearance();
        $appearance->setWidthType(
            $faker->randomConstantFromClass(Appearance::class, 'WIDTH_')
        );
        if ($faker->boolean) {
            $appearance->setBackgroundColor($faker->hexcolor);
            $appearance->setTextColor($faker->hexcolor);
            $appearance->setBackgroundImage($faker->optional(0.4)->imageUrl);
        }
        elseif ($faker->boolean) {
            $appearance->setTheme($faker->randomElement(['bieszczady', 'wood', 'rainbow', 'night']));
        }

        return $radioTable;
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
