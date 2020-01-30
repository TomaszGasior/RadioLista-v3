<?php

namespace App\DataFixtures;

use App\Entity\RadioTable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

class RadioTableFixtures extends AbstractFixture implements DependentFixtureInterface
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

            $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));

            return $radioTable;
        }

        $radioTable->setName($faker->words(rand(3, 8), true));
        $radioTable->setOwner($this->getReferenceFrom(UserFixtures::class));
        $radioTable->setDescription($faker->optional()->HTMLDescription);
        $radioTable->setUseKhz($faker->boolean(25));

        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', $faker->dateTimeBetween($radioTable->getOwner()->getRegisterDate(), $radioTable->getOwner()->getLastActivityDate()));

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
        $appearance['full'] = $faker->boolean;
        if ($faker->boolean) {
            $appearance['bg'] = $faker->hexcolor;
            $appearance['fg'] = $faker->hexcolor;
            $appearance['img'] = $faker->optional(0.4)->imageUrl;
        }
        elseif ($faker->boolean) {
            $appearance['th'] = $faker->randomElement(['bieszczady', 'wood', 'rainbow', 'night']);
        }
        $radioTable->setAppearance($appearance);

        return $radioTable;
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
