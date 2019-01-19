<?php

namespace App\Doctrine\EntityListener;

use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @PreUpdate
     */
    public function refreshLastActivityDate(User $user, PreUpdateEventArgs $args): void
    {
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
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $user->getPlainPassword())
            );
        }
    }
}
