<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser as BaseKernelBrowser;

class KernelBrowser extends BaseKernelBrowser
{
    public function loginUserByName(string $username): self
    {
        $container = $this->getContainer()->get('test.service_container');
        $userRepository = $container->get(UserRepository::class);

        $user = $userRepository->findOneByName($username);

        return $this->loginUser($user);
    }
}
