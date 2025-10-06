<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Domain\Shared\Contract\TimestampAwareInterface;
use App\Domain\Shared\Trait\Timestamp;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist, priority: 0)]
#[AsDoctrineListener(event: Events::preUpdate, priority: 0)]
class InitializeTimestampsListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof TimestampAwareInterface) {
            $entity->stampOnCreate();
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof TimestampAwareInterface) {
            $entity->stampOnUpdate();

            $em = $args->getObjectManager();
            $uow = $em->getUnitOfWork();
            $meta = $em->getClassMetadata($entity::class);
            $uow->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }
}
