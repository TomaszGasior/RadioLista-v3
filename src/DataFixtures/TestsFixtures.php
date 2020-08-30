<?php

namespace App\DataFixtures;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestsFixtures extends Fixture
{
    use FixtureTrait;

    public function load(ObjectManager $manager)
    {
        $user = (new User)
            ->setName('test_user')
            ->setPasswordHash('test_user')
            ->setPublicProfile(true)
            ->setAboutMe('test_user_about_me')
        ;

        $secondUser = (new User)
            ->setName('test_user_second')
            ->setPasswordHash('test_user_second')
        ;

        $adminUser = (new User)
            ->setName('test_user_admin')
            ->setPasswordHash('test_user_admin')
        ;
        $this->setPrivateFieldOfObject($adminUser, 'admin', true);

        $radioTable = (new RadioTable)
            ->setOwner($user)
            ->setName('test_radio_table_name')
            ->setStatus(RadioTable::STATUS_PUBLIC)
            ->setDescription('test_radio_table_description')
            ->setColumns(
                $this->getPrefixedConstantsOfClass(RadioTable::class, 'COLUMN_')
            )
        ;
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', new \DateTime('2018-05-01'));

        $radioStation = (new RadioStation)
            ->setRadioTable($radioTable)
            ->setName('test_radio_station_name')
            ->setFrequency(100.95)
            ->setPolarization(RadioStation::POLARIZATION_HORIZONTAL)
        ;

        $secondRadioStation = (new RadioStation)
            ->setRadioTable($radioTable)
            ->setName('test_second_radio_station_name')
            ->setFrequency(91.20)
            ->setPolarization(RadioStation::POLARIZATION_VERTICAL)
        ;

        foreach ([$user, $secondUser, $adminUser, $radioTable, $radioStation, $secondRadioStation] as $entity) {
            $manager->persist($entity);
            $manager->flush();
        }
    }
}
