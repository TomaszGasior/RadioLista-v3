<?php

namespace App\Doctrine\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    private $passwordEncoder;
    private $enableDateTimeRefresh;

    public function __construct(UserPasswordHasherInterface $passwordEncoder,
                                bool $enableDateTimeRefresh)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->enableDateTimeRefresh = $enableDateTimeRefresh;
    }

    /**
     * @PreUpdate
     */
    public function refreshLastActivityDate(User $user, PreUpdateEventArgs $args): void
    {
        if (false === $this->enableDateTimeRefresh) {
            return;
        }

        if ($args->hasChangedField('aboutMe') || $args->hasChangedField('publicProfile')) {
            $user->refreshLastActivityDate();
        }
    }

    /**
     * @PreFlush
     */
    public function encodePlainPassword(User $user): void
    {
        if ($user->getPlainPassword()) {
            $user->setPasswordHash(
                $this->passwordEncoder->hashPassword($user, $user->getPlainPassword())
            );
            $user->eraseCredentials();
        }
    }
}
