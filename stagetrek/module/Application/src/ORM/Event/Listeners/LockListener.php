<?php

namespace Application\ORM\Event\Listeners;

use Application\Entity\Interfaces\LockableEntityInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;

/**
 * @desc Interdit la destruction d'une entité ayant le tag LOCK
 */
class LockListener implements EventSubscriber
{

    /**
     * @throws \Exception
     */
    protected function assertDelete(LifecycleEventArgs $args) : bool
    {
        $entity = $args->getObject();
        if ($entity instanceof LockableEntityInterface) {
            if($entity->isLocked()){
                throw new Exception("L'entité est vérouillée. Suppression interdite");
            }
        }
        return true;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function preRemove(PreRemoveEventArgs $args)
    {
        $this->assertDelete($args);
    }

    public function getSubscribedEvents(): array
    {
        return [Events::preRemove];
    }
}