<?php

namespace App\Doctrine\EntityListener;

use App\Entity\RadioTable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Mapping\PreFlush;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreRemove;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsEntityListener]
class RadioTableListener
{
    use EntityListenerTrait;

    public function __construct(
        #[Autowire('@=null !== service("request_stack").getCurrentRequest()')]
        private bool $enableDateTimeRefresh,
    ) {}

    #[PreFlush]
    #[PreRemove]
    public function refreshLastActivityDateOfUser(RadioTable $radioTable, EventArgs $args): void
    {
        if (false === $this->enableDateTimeRefresh) {
            return;
        }

        $user = $radioTable->getOwner();

        $user->refreshLastActivityDate();
        $this->forceEntityUpdate($user, $args);
    }

    #[PrePersist]
    public function increaseRadioTablesCountOfUser(RadioTable $radioTable, EventArgs $args): void
    {
        $user = $radioTable->getOwner();

        $user->increaseRadioTablesCount();
        $this->forceEntityUpdate($user, $args);
    }

    #[PreRemove]
    public function decreaseRadioTablesCountOfUser(RadioTable $radioTable, EventArgs $args): void
    {
        $user = $radioTable->getOwner();

        $user->decreaseRadioTablesCount();
        $this->forceEntityUpdate($user, $args);
    }
}
