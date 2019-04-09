<?php

namespace App\Security\Voter;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AdminVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        if ('USER_PUBLIC_PROFILE' === $attribute && $subject instanceof User) {
            return true;
        }
        if ('RADIOTABLE_SHOW' === $attribute && $subject instanceof RadioTable) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
