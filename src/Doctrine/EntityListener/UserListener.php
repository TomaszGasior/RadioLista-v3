<?php

namespace App\Doctrine\EntityListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsEntityListener]
class UserListener
{
    public function __construct(
        #[Autowire('@=null !== service("request_stack").getCurrentRequest()')]
        private bool $enableDateTimeRefresh,
    ) {}

    #[PreUpdate]
    public function refreshLastActivityDate(User $user, PreUpdateEventArgs $args): void
    {
        if (false === $this->enableDateTimeRefresh) {
            return;
        }

        if ($args->hasChangedField('aboutMe') || $args->hasChangedField('publicProfile')) {
            $user->refreshLastActivityDate();
        }
    }
}
