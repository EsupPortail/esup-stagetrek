<?php

namespace Application\ORM\Event\Listeners;

use Application\Entity\Db\Source;
use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasSourceInterface;
use Application\Entity\Interfaces\LockableEntityInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use RuntimeException;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;

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
    public function getSubscribedEvents(): array
    {
        return [Events::preRemove];
    }
}