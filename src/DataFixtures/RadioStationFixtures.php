<?php

namespace App\DataFixtures;

use App\Entity\Embeddable\RadioStation\Locality;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Generator;

class RadioStationFixtures extends AbstractEntityFixture implements DependentFixtureInterface
{
    protected const ENTITIES_NUMBER = 5000;

    protected function createEntity(Generator $faker, int $i): object
    {
        $radioStation = new RadioStation;

        // Radio tables for hardcoded users.
        $radioStation->setRadioTable(
            $this->getReferenceFrom(RadioTableFixtures::class, $i < 40 ? rand(1, 3) : null)
        );

        $radioStation->setFrequency(
            RadioTable::FREQUENCY_KHZ === $radioStation->getRadioTable()->getFrequencyUnit()
            ? $faker->randomFloat(1, 100, 9999)
            : $faker->randomFloat(1, 87.5, 108)
        );
        $radioStation->setPower($faker->randomFloat(2, 0, 1000));
        $radioStation->setPrivateNumber($faker->numberBetween(1, 100));

        $radioStation->setName($faker->radioStation);
        $radioStation->setRadioGroup($faker->optional()->radioGroup);
        $radioStation->setCountry($faker->optional()->country);
        $radioStation->setLocation($faker->optional()->city);
        $radioStation->setMultiplex($faker->optional()->multiplex);

        $radioStation->setPolarization(
            $faker->randomConstantFromClass(RadioStation::class, 'POLARIZATION_')
        );
        $radioStation->setQuality(
            $faker->randomConstantFromClass(RadioStation::class, 'QUALITY_')
        );
        $radioStation->setType(
            $faker->randomConstantFromClass(RadioStation::class, 'TYPE_')
        );
        $radioStation->setReception(
            $faker->randomConstantFromClass(RadioStation::class, 'RECEPTION_')
        );

        if ($faker->boolean(75)) {
            [$dabChannel, $frequency] = $faker->dabChannelWithFrequency;
            $radioStation
                ->setDabChannel($dabChannel)
                ->setFrequency($frequency)
            ;
        }
        if ($faker->boolean(75)) {
            $radioStation->setDistance($faker->numberBetween(1, 999));
        }
        if ($faker->boolean(75)) {
            $radioStation->setMaxSignalLevel($faker->numberBetween(1, 999));
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
            ($radioStation->getLocality())
                ->setType($faker->randomConstantFromClass(Locality::class, 'TYPE_'))
                ->setCity($faker->boolean(25) ? $faker->city : $faker->state)
            ;
        }
        if ($faker->boolean(25)) {
            $radioStation->setMarker(
                $faker->randomConstantFromClass(RadioStation::class, 'MARKER_')
            );
        }
        if ($faker->boolean) {
            ($radioStation->getRds())
                ->setPs(
                    $faker->boolean(40) ? [$faker->words(rand(1, 7))] :
                                          [$faker->words(rand(1, 7)), $faker->words(rand(1, 7))]
                )
                ->setRt($faker->optional(40, [])->sentences())
                ->setPty($faker->optional()->randomElement(
                    ['NEWS', 'INFO', 'SPORT', 'CULTURE', 'POP M', 'ROCK M', 'LIGHT M', 'CLASSIC', 'OTHER M']
                ))
                ->setPi(rand(1000, 9999))
            ;
        }
        if ($faker->boolean(40)) {
            $radioStation->setExternalAnchor($faker->url);
        }
        if ($faker->boolean) {
            $radioStation->setComment($faker->sentences(2, true));
        }

        return $radioStation;
    }

    public function getDependencies()
    {
        return [RadioTableFixtures::class];
    }
}
