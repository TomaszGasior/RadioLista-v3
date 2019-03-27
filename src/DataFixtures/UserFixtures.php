<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Generator;

class UserFixtures extends AbstractFixture
{
    public const ENTITIES_NUMBER = 30;

    private const DEFAULT_USER_NAME = 'radiolista';
    private const DEFAULT_USER_PASS = 'radiolista';
    private const TEST_USER_NAME = 'test_user';
    private const TEST_USER_PASS = 'test_user';

    protected function createEntity(Generator $faker, int $i): object
    {
        $user = new User;

        $user->setName($faker->unique()->username);
        $user->setPublicProfile($faker->boolean(75));

        if (1 === $i) {
            $user->setName(self::DEFAULT_USER_NAME);
            $user->setPublicProfile(true);
            $user->setPlainPassword(self::DEFAULT_USER_PASS);
        }
        elseif (2 === $i) {
            $user->setName(self::TEST_USER_NAME);
            $user->setPublicProfile(false);
            $user->setPlainPassword(self::TEST_USER_PASS);
        }
        else {
            $user->setPlainPassword($user->getName());
        }

        $user->setAboutMe($faker->optional()->HTMLDescription);

        return $user;
    }
}
