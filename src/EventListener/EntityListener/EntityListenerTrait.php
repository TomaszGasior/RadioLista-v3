<?php

namespace App\EventListener\EntityListener;

use Doctrine\Common\EventArgs;

trait EntityListenerTrait
{
    // There is need to manually enforce update of associated entities,
    // for example when User entity is modified inside RadioTable entity event.
    // It's because associations are not tracked consistently inside Doctrine's events.
    private function forceEntityUpdate(object $entity, EventArgs $args): void
    {
        $entityManager = $args->getEntityManager();

        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $entityManager->getClassMetadata(get_class($entity)),
            $entity
        );
    }
}
