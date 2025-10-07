<?php

namespace App\Tests;

use App\Repository\UserRepository;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait LoginUserTrait
{
    private function loginUserByName(KernelBrowser $client, string $username): void
    {
        if (!$this instanceof KernelTestCase) {
            throw new RuntimeException;
        }

        /** @var UserRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        $client->loginUser($userRepository->findOneBy(['name' => $username]));
    }
}
