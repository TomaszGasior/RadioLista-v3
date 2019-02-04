<?php

namespace App\Security\Voter;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RadioTableVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['RADIOTABLE_MODIFY', 'RADIOTABLE_SHOW'])
               && ($subject instanceof RadioTable || $subject instanceof RadioStation);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $radioTable = ($subject instanceof RadioStation) ? $subject->getRadioTable() : $subject;

        switch ($attribute) {
            case 'RADIOTABLE_SHOW':
                if (in_array($radioTable->getStatus(), [RadioTable::STATUS_PUBLIC, RadioTable::STATUS_UNLISTED])) {
                    return true;
                }
                // Don't break.

            case 'RADIOTABLE_MODIFY':
                if ($user and $user === $radioTable->getOwner()) {
                    return true;
                }
        }

        return false;
    }
}
