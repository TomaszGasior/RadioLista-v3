<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Generator;

class UserFixtures extends AbstractEntityFixture
{
    protected const ENTITIES_NUMBER = 30;

    private const DEFAULT_USERNAME = 'radiolista';

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

        $user->setPlainPassword($user->getName());
        $user->setAboutMe($faker->optional()->HTMLDescription);

        $registerDate = $faker->dateTimeBetween('2012-07-01', '-1 year');
        $this->setPrivateFieldOfObject($user, 'registerDate', $registerDate);
        $this->setPrivateFieldOfObject($user, 'lastActivityDate', $faker->dateTimeBetween($registerDate, 'now'));

        return $user;
    }
}
