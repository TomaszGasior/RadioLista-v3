<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Generator;

class UserFixtures extends AbstractFixture
{
    public const ENTITIES_NUMBER = 30;

    protected function createEntity(Generator $faker, int $i): object
    {
        $user = new User;

        $user->setName($faker->unique()->username);
        $user->setPublicProfile($faker->boolean(75));

        if (1 === $i) {
            $user->setName('radiolista');
            $user->setPublicProfile(true);
        }

        $user->setPlainPassword($user->getName());
        $user->setAboutMe($faker->optional()->HTMLDescription);

        return $user;
    }
}
