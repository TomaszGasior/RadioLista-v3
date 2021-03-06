<?php

namespace App\Security\Voter;

use App\Entity\RadioTable;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Contracts\EventDispatcher\Event;

class AdminVoter extends Voter
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function supports($attribute, $subject): bool
    {
        if ('USER_PUBLIC_PROFILE' === $attribute && $subject instanceof User) {
            return true;
        }
        if ('RADIO_TABLE_SHOW' === $attribute && $subject instanceof RadioTable) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!($currentUser instanceof User) || !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return false;
        }

        if ($subject instanceof User) {
            $user = $subject;

            $result = (false === $user->getPublicProfile() && $user !== $currentUser);
        }
        if ($subject instanceof RadioTable) {
            $radioTable = $subject;

            $result = (RadioTable::STATUS_PRIVATE === $radioTable->getStatus()
                       && $radioTable->getOwner() !== $currentUser);
        }

        // This method could be shorten to just `return Security::isGranted('ROLE_ADMIN')`
        // but this whole logic is needed to show flash message when site administrator
        // browses users private contents. Take a look at AdminFlashMessage class.
        if ($result) {
            $this->eventDispatcher->dispatch(new Event, 'app.restricted_admin_access');
        }

        return $result;
    }
}
