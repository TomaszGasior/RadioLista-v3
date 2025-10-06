<?php

namespace App\Security\Voter;

use App\Entity\Enum\RadioTable\Status;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Event\AdminRestrictedAccessEvent;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    public function __construct(private EventDispatcherInterface $eventDispatcher) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ('USER_PUBLIC_PROFILE' === $attribute && $subject instanceof User) {
            return true;
        }
        if ('RADIO_TABLE_SHOW' === $attribute && $subject instanceof RadioTable) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $currentUser = $token->getUser();

        if (false === $currentUser instanceof User || false === in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return false;
        }

        if ($subject instanceof User) {
            $user = $subject;

            $result = (false === $user->getPublicProfile() && $user !== $currentUser);
        }
        elseif ($subject instanceof RadioTable) {
            $radioTable = $subject;

            $result = (Status::PRIVATE === $radioTable->getStatus()
                       && $radioTable->getOwner() !== $currentUser);
        }
        else {
            throw new RuntimeException;
        }

        // This method could be shorten to just `return Security::isGranted('ROLE_ADMIN')`
        // but this whole logic is needed to show flash message when site administrator
        // browses users private contents. Take a look at AdminFlashMessage class.
        if ($result) {
            $this->eventDispatcher->dispatch(new AdminRestrictedAccessEvent);
        }

        return $result;
    }
}
