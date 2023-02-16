<?php

namespace App\Security\Voter;

use App\Entity\Enum\RadioTable\Status;
use App\Entity\RadioStation;
use App\Entity\RadioTable;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RadioTableVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['RADIO_TABLE_MODIFY', 'RADIO_TABLE_SHOW'])
               && ($subject instanceof RadioTable || $subject instanceof RadioStation);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($subject instanceof RadioStation) {
            $radioTable = $subject->getRadioTable();
        }
        elseif ($subject instanceof RadioTable) {
            $radioTable = $subject;
        }
        else {
            throw new RuntimeException;
        }

        switch ($attribute) {
            case 'RADIO_TABLE_SHOW':
                if (in_array($radioTable->getStatus(), [Status::PUBLIC, Status::UNLISTED])) {
                    return true;
                }
                // Don't break.

            case 'RADIO_TABLE_MODIFY':
                if ($user and $user === $radioTable->getOwner()) {
                    return true;
                }
        }

        return false;
    }
}
