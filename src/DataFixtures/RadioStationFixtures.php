<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioStation\Background;
use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioStation\Reception;
use App\Entity\Enum\RadioStation\Type;
use App\Entity\Enum\RadioTable\FrequencyUnit;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('dev')]
class RadioStationFixtures extends AbstractEntityFixture implements DependentFixtureInterface
{
    protected const ENTITIES_NUMBER = 16000;

    public function __construct(private Faker $faker) {}

    protected function createEntity(int $i): object
    {
        $radioTable = $this->getEntity(RadioTable::class);

        $radioStation = new RadioStation(
            frequency: match ($radioTable->getFrequencyUnit()) {
                FrequencyUnit::MHZ => $this->faker->randomFloat(1, 87.5, 108),
                FrequencyUnit::KHZ => $this->faker->randomFloat(1, 100, 9999),
            },
            name: $this->faker->radioStation(),
            radioTable: $radioTable,
        );

        $radioStation->setPower($this->faker->randomFloat(2, 0, 1000));
        $radioStation->setPrivateNumber($this->faker->numberBetween(1, 100));

        $radioStation->setRadioGroup($this->faker->optional()->radioGroup());
        $radioStation->setCountry($this->faker->optional()->country());
        $radioStation->setLocation($this->faker->optional()->city());
        $radioStation->setMultiplex($this->faker->optional()->multiplex());

        $radioStation->setPolarization($this->faker->optional()->randomEnum(Polarization::class));
        $radioStation->setQuality($this->faker->randomEnum(Quality::class));
        $radioStation->setType($this->faker->randomEnum(Type::class));
        $radioStation->setReception($this->faker->randomEnum(Reception::class));

        if ($this->faker->boolean(75)) {
            $dabChannel = $this->faker->randomEnum(DabChannel::class);
            $radioStation
                ->setDabChannel($dabChannel)
                ->setFrequency($dabChannel->getFrequency())
            ;
        }
        if ($this->faker->boolean(75)) {
            $radioStation->setDistance($this->faker->numberBetween(1, 999));
        }
        if ($this->faker->boolean(75)) {
            $radioStation->setMaxSignalLevel($this->faker->numberBetween(1, 999));
        }
        if ($this->faker->boolean(75)) {
            $firstLogDate = $this->faker->dateTimeBetween('-25 years', 'now')->format('Y-m-d');
            if ($this->faker->boolean()) {
                $firstLogDate = substr($firstLogDate, 0, -3);
                if ($this->faker->boolean()) {
                    $firstLogDate = substr($firstLogDate, 0, -3);
                }
            }
            $radioStation->setFirstLogDate($firstLogDate);
        }
        if ($this->faker->boolean()) {
            $radioStation->setRegion(
                $this->faker->boolean() ? $this->faker->city() : $this->faker->state()
            );
        }
        if ($this->faker->boolean(25)) {
            ($radioStation->getAppearance())
                ->setBackground($this->faker->randomEnum(Background::class))
            ;
        }
        if ($this->faker->boolean(25)) {
            ($radioStation->getAppearance())
                ->setBold($this->faker->boolean())
                ->setItalic($this->faker->boolean())
                ->setStrikethrough($this->faker->boolean())
            ;
        }
        if ($this->faker->boolean()) {
            ($radioStation->getRds())
                ->setPs(
                    $this->faker->boolean(40) ? [$this->faker->words(rand(1, 7))] :
                                          [$this->faker->words(rand(1, 7)), $this->faker->words(rand(1, 7))]
                )
                ->setRt($this->faker->optional(0.4, [])->sentences())
                ->setPty($this->faker->optional()->randomElement(
                    ['NEWS', 'INFO', 'SPORT', 'CULTURE', 'POP M', 'ROCK M', 'LIGHT M', 'CLASSIC', 'OTHER M']
                ))
                ->setPi(rand(1000, 9999))
            ;
        }
        if ($this->faker->boolean(40)) {
            $radioStation->setExternalAnchor($this->faker->url());
        }
        if ($this->faker->boolean()) {
            $radioStation->setComment($this->faker->sentences(2, true));
        }

        return $radioStation;
    }

    public function getDependencies(): array
    {
        return [RadioTableFixtures::class];
    }
}
