<?php

namespace App\Doctrine\EventListener;

use App\Entity\RadioStation;
use App\Entity\RadioTable;
use App\Entity\User;
use App\Event\RadioStationCreated;
use App\Event\RadioStationRemoved;
use App\Event\RadioStationUpdated;
use App\Event\RadioTableCreated;
use App\Event\RadioTableRemoved;
use App\Event\RadioTableUpdated;
use App\Event\UserUpdated;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * This Doctrine event listener dispatches custom, entity-specific
 * events for insert, update and delete operations.
 *
 * It's hooked into Doctrine's onFlush event, which is triggered in the middle
 * of EntityManager::flush() operation - right after scheduling all operations
 * and just before starting database transaction.
 *
 * This approach makes modifying entities within these events easier,
 * more consistent and predictable than using other Doctrine events like
 * preFlush, prePersist, preUpdate, preRemove, which are inconsistent and
 * hard to understand without digging into Doctrine implementation details.
 *
 * Update events are only triggered when entities are actually modified.
 */
#[AsDoctrineListener(Events::onFlush)]
class EventDispatchingListener
{
    public function __construct(private EventDispatcherInterface $eventDispatcher) {}

    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new RuntimeException;
        }

        $unitOfWork = $entityManager->getUnitOfWork();

        $this->dispatchEntityEvents($unitOfWork);
        $this->recomputeEntityChangeSets($entityManager, $unitOfWork);
    }

    private function getEventForEntityInsert(object $entity): ?object
    {
        return match ($entity::class) {
            RadioTable::class => new RadioTableCreated($entity),
            RadioStation::class => new RadioStationCreated($entity),
            default => null,
        };
    }

    private function getEventForEntityUpdate(object $entity, UnitOfWork $unitOfWork): ?object
    {
        return match ($entity::class) {
            RadioTable::class => new RadioTableUpdated($entity),
            RadioStation::class => new RadioStationUpdated($entity),
            User::class => new UserUpdated($entity, array_keys($unitOfWork->getEntityChangeSet($entity))),
            default => null,
        };
    }

    private function getEventForEntityDelete(object $entity): ?object
    {
        return match ($entity::class) {
            RadioTable::class => new RadioTableRemoved($entity),
            RadioStation::class => new RadioStationRemoved($entity),
            default => null,
        };
    }

    private function dispatchEntityEvents(UnitOfWork $unitOfWork): void
    {
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->dispatchEvent($this->getEventForEntityInsert($entity));
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->dispatchEvent($this->getEventForEntityUpdate($entity, $unitOfWork));
        }

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            $this->dispatchEvent($this->getEventForEntityDelete($entity));
        }
    }

    private function dispatchEvent(?object $event): void
    {
        if ($event) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    private function recomputeEntityChangeSets(EntityManagerInterface $entityManager, UnitOfWork $unitOfWork): void
    {
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $metadata = $entityManager->getClassMetadata($entity::class);

            if ($unitOfWork->getEntityChangeSet($entity)) {
                $unitOfWork->recomputeSingleEntityChangeSet($metadata, $entity);
            }
            else {
                $unitOfWork->computeChangeSet($metadata, $entity);
            }
        }

        foreach ($unitOfWork->getIdentityMap() as $class => $entities) {
            $metadata = $entityManager->getClassMetadata($class);

            foreach ($entities as $entity) {
                if (!$unitOfWork->isScheduledForDelete($entity)) {
                    $unitOfWork->recomputeSingleEntityChangeSet($metadata, $entity);
                }
            }
        }
    }
}
