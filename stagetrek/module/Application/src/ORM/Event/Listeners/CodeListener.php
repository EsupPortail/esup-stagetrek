<?php

namespace Application\ORM\Event\Listeners;

use Application\Entity\Interfaces\CodeEntityInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;

/**
 * Listener Doctrine.
 *
 * Renseigne si besoin l'heure et l'auteur de la création/modification
 * de toute entité dont la classe implémente HistoriqueAwareInterface.
 *
 * Déclenchement : avant que l'enregistrement ne soit persisté (création) ou mis à jour (update).
 *
 * @author Bertrand GAUTHIER <bertrand.gauthier at unicaen.fr>
 * @see HistoriqueAwareInterface
 *
 * todo /!\ why not /!\ eclater en deux un dans unicaen/application pour la date et un second dans utilisateur pour les utilisateurs
 */
class CodeListener implements EventSubscriber
{
    /**
     * @param LifecycleEventArgs $args
     * @throws RuntimeException Aucun utilisateur disponible pour en faire l'auteur de la création/modification
     */


    protected function generateCode(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        // l'entité doit implémenter l'interface requise
        if (! $entity instanceof CodeEntityInterface) {
            return;
        }
//        Si l'entité à déjà un code on ne le génére pas
        if($entity->getCode() !== null && $entity->getCode() !== ""){return;}
        $uid = $entity->getId();
        if($uid === null || $uid==0) {
            /** @varEntityManager $manager */
            $entityManager = $args->getObjectManager();
            $last = $entityManager->getRepository($entity::class)->findOneBy([], ['id' => 'desc']);
            $uid = ($last) ? ($last->getId() + 1) : 1; //il s'agira probablement du prochain id
            // on n'utilise pas la sequence car son appel fait gonfler artificiellement les id
        }
        $param['uid'] = $uid;
        $code = $entity->generateDefaultCode($param);
        $entity->setCode($code);
    }


    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->generateCode($args);
    }
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::prePersist];
    }
}