<?php

namespace App\Doctrine\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PreUpdate;

class UserListener
{
    public function __construct(private bool $enableDateTimeRefresh) {}

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
