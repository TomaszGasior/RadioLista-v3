<?php

namespace App\Doctrine\EntityListener;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;

trait EntityListenerTrait
{
    /**
     * There is need to manually enforce update of associated entities,
     * for example when User entity is modified inside RadioTable entity event.
     * It's because associations are not tracked consistently inside Doctrine's
     * events.
     *
     * @param LifecycleEventArgs|PreFlushEventArgs|PreFlushEventArgs $args
     */
    private function forceEntityUpdate(object $entity, EventArgs $args): void
    {
        $entityManager = $args->getEntityManager();

        // Don't try to enforce update if entity is not managed by Doctrine
        // (this happens when associated entity was removed right now).
        if (false === $entityManager->contains($entity)) {
            return;
        }

        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $entityManager->getClassMetadata(get_class($entity)),
            $entity
        );
    }
}
