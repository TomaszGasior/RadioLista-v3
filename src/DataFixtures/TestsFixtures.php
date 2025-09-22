<?php

namespace App\DataFixtures;

use App\Entity\Enum\RadioStation\Polarization;
use App\Entity\Enum\RadioTable\Column;
use App\Entity\Enum\RadioTable\Status;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Util\ReflectionUtilsTrait;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[When('test')]
class TestsFixtures extends Fixture
{
    use ReflectionUtilsTrait;

    public function __construct(private UserPasswordHasherInterface $passwordEncoder) {}

    public function load(ObjectManager $manager): void
    {
        $user = (new User('test_user'))
            ->setPublicProfile(true)
            ->setAboutMe('test_user_about_me')
        ;
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'test_password_user'));

        $secondUser = new User('test_user_second');
        $secondUser->setPassword($this->passwordEncoder->hashPassword($secondUser, 'test_password_user_second'));

        $adminUser = new User('test_user_admin');
        $adminUser->setPassword($this->passwordEncoder->hashPassword($adminUser, 'test_password_user_admin'));
        $this->setPrivateFieldOfObject($adminUser, 'admin', true);

        $radioTable = (new RadioTable('test_radio_table_name', $user))
            ->setStatus(Status::PUBLIC)
            ->setDescription('test_radio_table_description')
            ->setColumns(Column::cases())
        ;
        $this->setPrivateFieldOfObject($radioTable, 'lastUpdateTime', new DateTimeImmutable('2018-05-01'));

        $radioStation = (new RadioStation('100.95', 'test_radio_station_name', $radioTable))
            ->setPolarization(Polarization::HORIZONTAL)
        ;
        $radioStation->getRds()
            ->setRt(['test radio station'])
        ;

        $secondRadioStation = (new RadioStation('91.20', 'test_second_radio_station_name', $radioTable))
            ->setPolarization(Polarization::VERTICAL)
        ;

        $thirdRadioStation = (new RadioStation('101.605', 'test_third_radio_station_name', $radioTable));

        foreach ([$user, $secondUser, $adminUser, $radioTable, $radioStation, $secondRadioStation, $thirdRadioStation] as $entity) {
            $manager->persist($entity);
            $manager->flush();
        }
    }
}
