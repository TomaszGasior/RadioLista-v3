<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Generator;

class UserFixtures extends AbstractFixture
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

            $this->setUserAdmin($user);
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

        return $user;
    }

    private function setUserAdmin(User $user): void
    {
        $reflection = new \ReflectionProperty(User::class, 'admin');
        $reflection->setAccessible(true);
        $reflection->setValue($user, true);
    }
}
