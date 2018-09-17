<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class RehashRLv1UserPassword
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();

        $user          = $token->getUser();
        $plainPassword = $token->getCredentials();

        // Normally Security takes care about clearing sensitive data from token manager,
        // however now it's needed to remove credentials manually because of current
        // Security settings ("erase_credentials" is set to "false").
        $token->eraseCredentials();

        if ($plainPassword && 'rl_v1' === $user->getEncoderName()) {
            $user->setPlainPassword($plainPassword);
            $this->entityManager->flush($user);
        }
    }
}
