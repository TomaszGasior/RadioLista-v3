<?php

namespace App\DataFixtures;

use App\Entity\Embeddable\RadioStation\Appearance;
use App\Entity\Enum\RadioStation\Background;
use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioStation\Type;
use App\Entity\Enum\RadioTable\FrequencyUnit;
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
            FrequencyUnit::KHZ === $radioStation->getRadioTable()->getFrequencyUnit()
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

        $radioStation->setPolarization($faker->optional->randomEnum(Polarization::class));
        $radioStation->setQuality($faker->randomEnum(Quality::class));
        $radioStation->setType($faker->randomEnum(Type::class));
        $radioStation->setReception($faker->randomEnum(Reception::class));

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
            $radioStation->setRegion(
                $faker->boolean ? $faker->city : $faker->state
            );
        }
        if ($faker->boolean(25)) {
            ($radioStation->getAppearance())
                ->setBackground($faker->randomEnum(Background::class))
            ;
        }
        if ($faker->boolean(25)) {
            ($radioStation->getAppearance())
                ->setBold($faker->boolean)
                ->setItalic($faker->boolean)
                ->setStrikethrough($faker->boolean)
            ;
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

    public function getDependencies(): array
    {
        return [RadioTableFixtures::class];
    }
}
