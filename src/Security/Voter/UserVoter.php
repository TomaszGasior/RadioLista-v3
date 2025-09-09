<?php

namespace App\Security\Voter;

use App\Entity\User;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'USER_PUBLIC_PROFILE' === $attribute
               && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        if ($subject instanceof User) {
            $user = $subject;
        }
        else {
            throw new RuntimeException;
        }

        return $user->getPublicProfile();
    }
}
