<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Util\ReflectionUtilsTrait;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestsFixtures extends Fixture
{
    use ReflectionUtilsTrait;

    public function __construct(private UserPasswordHasherInterface $passwordEncoder) {}

    public function load(ObjectManager $manager): void
    {
        $user = (new User)
            ->setName('test_user')
            ->setPublicProfile(true)
            ->setAboutMe('test_user_about_me')
        ;
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'test_password_user'));

        $secondUser = (new User)
            ->setName('test_user_second')
        ;
        $secondUser->setPassword($this->passwordEncoder->hashPassword($secondUser, 'test_password_user_second'));

        $adminUser = (new User)
            ->setName('test_user_admin')
        ;
        $adminUser->setPassword($this->passwordEncoder->hashPassword($adminUser, 'test_password_user_admin'));
        $this->setPrivateFieldOfObject($adminUser, 'admin', true);

        $radioTable = (new RadioTable)
            ->setOwner($user)
            ->setName('test_radio_table_name')
            ->setStatus(Status::PUBLIC)
            ->setDescription('test_radio_table_description')
            ->setColumns(Column::cases())
        ;
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', new DateTime('2018-05-01'));

        $radioStation = (new RadioStation)
            ->setRadioTable($radioTable)
            ->setName('test_radio_station_name')
            ->setFrequency(100.95)
            ->setPolarization(Polarization::HORIZONTAL)
        ;
        $radioStation->getRds()
            ->setRt(['test radio station'])
        ;

        $secondRadioStation = (new RadioStation)
            ->setRadioTable($radioTable)
            ->setName('test_second_radio_station_name')
            ->setFrequency(91.20)
            ->setPolarization(Polarization::VERTICAL)
        ;

        $thirdRadioStation = (new RadioStation)
            ->setRadioTable($radioTable)
            ->setName('test_third_radio_station_name')
            ->setFrequency(101.605)
        ;

        foreach ([$user, $secondUser, $adminUser, $radioTable, $radioStation, $secondRadioStation, $thirdRadioStation] as $entity) {
            $manager->persist($entity);
            $manager->flush();
        }
    }
}
