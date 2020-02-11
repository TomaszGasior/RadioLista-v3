<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Generator;

class UserFixtures extends AbstractEntityFixture
{
    protected const ENTITIES_NUMBER = 30;

    public const DEFAULT_USERNAME = 'radiolista';
    public const TEST_USERNAME = 'test_user';
    public const TEST_USERNAME_SECOND = 'test_user_2';

    protected function createEntity(Generator $faker, int $i): object
    {
        $user = new User;

        $user->setName($faker->unique()->username);
        $user->setPublicProfile($faker->boolean(75));

        if (1 === $i) {
            $user->setName(self::DEFAULT_USERNAME);
            $user->setPublicProfile(true);

            $this->setPrivateFieldOfObject($user, 'admin', true);
        }
        elseif (2 === $i) {
            $user->setName(self::TEST_USERNAME);
            $user->setPublicProfile(false);
        }
        elseif (3 === $i) {
            $user->setName(self::TEST_USERNAME_SECOND);
            $user->setPublicProfile(false);
        }

        $user->setPlainPassword($user->getName());
        $user->setAboutMe($faker->optional()->HTMLDescription);

        $registerDate = $faker->dateTimeBetween('2012-07-01', '-1 year');
        $this->setPrivateFieldOfObject($user, 'registerDate', $registerDate);
        $this->setPrivateFieldOfObject($user, 'lastActivityDate', $faker->dateTimeBetween($registerDate, 'now'));

        return $user;
    }
}
