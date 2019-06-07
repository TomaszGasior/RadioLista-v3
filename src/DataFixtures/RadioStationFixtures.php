<?php

namespace App\DataFixtures;

use App\Entity\RadioStation;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

class RadioStationFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected const ENTITIES_NUMBER = 5000;

    protected function createEntity(Generator $faker, int $i): object
    {
        $radioStation = new RadioStation;

        // Radiotables for hardcoded users.
        $radioStation->setRadioTable(
            $this->getReferenceFrom(RadioTableFixtures::class, $i < 40 ? rand(1, 3) : null)
        );

        $radioStation->setFrequency(
            $radioStation->getRadioTable()->getUseKhz()
            ? $faker->randomFloat(1, 100, 9999)
            : $faker->randomFloat(1, 87.5, 108)
        );
        $radioStation->setPower($faker->randomFloat(2, 0, 1000));
        $radioStation->setPrivateNumber($faker->numberBetween(1, 100));

        $radioStation->setName($faker->radioStation);
        $radioStation->setRadioGroup($faker->optional()->radioGroup);
        $radioStation->setCountry($faker->optional()->country);
        $radioStation->setLocation($faker->optional()->city);

        $radioStation->setPolarization(
            $faker->randomConstantFromClass(RadioStation::class, 'POLARIZATION_')
        );
        $radioStation->setQuality(
            $faker->randomConstantFromClass(RadioStation::class, 'QUALITY_')
        );
        $radioStation->setType(
            $faker->randomConstantFromClass(RadioStation::class, 'TYPE_')
        );
        if ($faker->boolean(75)) {
            $radioStation->setDistance($faker->numberBetween(1, 999));
        }
        if ($faker->boolean(75)) {
            $firstLogDate = $faker->dateTimeBetween('-25 years', 'now')->format('Y-m-d');
            if ($faker->boolean) {
                $firstLogDate = substr($firstLogDate, 0, -3);
                if ($faker->boolean) {
                    $firstLogDate = substr($firstLogDate, 0, -3);
                }
            }
            $radioStation->setFirstLogDate($firstLogDate);
        }
        if ($faker->boolean) {
            $radioStation->setLocality([
                'type' => $faker->randomConstantFromClass(RadioStation::class, 'LOCALITY_'),
                'city' => $faker->boolean(25) ? $faker->city : $faker->state,
            ]);
        }
        if ($faker->boolean(25)) {
            $radioStation->setMarker(
                $faker->randomConstantFromClass(RadioStation::class, 'MARKER_')
            );
        }
        if ($faker->boolean) {
            $radioStation->setRds([
                'ps' => implode('|', $faker->words(rand(1, 7))),
                'rt' => $faker->boolean(40) ? implode('|', $faker->sentences()) : null,
                'pty' => $faker->optional()->randomElement(
                    ['NEWS', 'INFO', 'SPORT', 'CULTURE', 'POP M', 'ROCK M', 'LIGHT M', 'CLASSIC', 'OTHER M']
                ),
                'pi' => $faker->randomNumber(4),
            ]);
        }

        return $radioStation;
    }

    public function getDependencies()
    {
        return [RadioTableFixtures::class];
    }
}
