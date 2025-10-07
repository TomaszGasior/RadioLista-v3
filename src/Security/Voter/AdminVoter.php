<?php

namespace App\Security\Voter;

use App\Entity\RadioTable;
use App\Entity\User;
use App\Event\AdminRestrictedAccessEvent;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

#[AutoconfigureTag('security.voter', ['priority' => -1])]
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
        $user = $token->getUser();

        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            // Thanks to "-1" priority in "security.voter" tag
            // this event is only dispatched if other voters denied access.
            $this->eventDispatcher->dispatch(new AdminRestrictedAccessEvent);

            return true;
        }

        return false;
    }
}
