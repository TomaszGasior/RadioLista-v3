<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioStation\Background;
use App\Entity\Enum\RadioStation\DabChannel;
use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioStation\Quality;
use App\Entity\Enum\RadioStation\Reception;
use App\Entity\Enum\RadioStation\Type;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When('dev')]
class RadioStationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/fixtures/radio_station.php')] private string $source,
    ) {}

    public function load(ObjectManager $manager): void
    {
        ini_set('memory_limit', '2G');

        foreach (include $this->source as $id => $data) {
            [$radioTableId, $name, $radioGroup, $country, $region, $frequency, $location, $power, $polarization, $multiplex, $dabChannel, $distance, $maxSignalLevel, $reception, $privateNumber, $firstLogDate, $quality, $type, $rdsPs, $rdsRt, $rdsPty, $rdsPi, $comment, $externalAnchor, $appearanceBackground, $appearanceBold, $appearanceItalic, $appearanceStrikethrough] = $data;

            $radioTable = $this->getReference(RadioTable::class . $radioTableId, RadioTable::class);

            $radioStation = (new RadioStation($frequency, $name, $radioTable))
                ->setRadioGroup($radioGroup)
                ->setCountry($country)
                ->setRegion($region)
                ->setLocation($location)
                ->setPower($power)
                ->setPolarization($polarization ? Polarization::from($polarization) : null)
                ->setMultiplex($multiplex)
                ->setDabChannel($dabChannel ? DabChannel::from($dabChannel) : null)
                ->setDistance($distance)
                ->setMaxSignalLevel($maxSignalLevel)
                ->setReception(Reception::from($reception))
                ->setPrivateNumber($privateNumber)
                ->setFirstLogDate($firstLogDate)
                ->setQuality(Quality::from($quality))
                ->setType(Type::from($type))
                ->setComment($comment)
                ->setExternalAnchor($externalAnchor)
            ;

            $radioStation->getRds()
                ->setPi($rdsPi)
                ->setPs($rdsPs)
                ->setPty($rdsPty)
                ->setRt($rdsRt)
            ;

            $radioStation->getAppearance()
                ->setBackground($appearanceBackground ? Background::from($appearanceBackground) : null)
                ->setBold($appearanceBold)
                ->setItalic($appearanceItalic)
                ->setStrikethrough($appearanceStrikethrough)
            ;

            $manager->persist($radioStation);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RadioTableFixtures::class];
    }
}
