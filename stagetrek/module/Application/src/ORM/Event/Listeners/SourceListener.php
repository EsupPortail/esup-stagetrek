<?php

namespace Application\ORM\Event\Listeners;

use Application\Entity\Db\Source;
use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasSourceInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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
class SourceListener implements EventSubscriber
{
    protected ?string $sourceEntityClass = null;

    public function setSourceEntityClass(string $sourceEntityClass): self
    {
        $this->sourceEntityClass = $sourceEntityClass;
        return $this;
    }

    protected ?string $defaultSourceCode = null;

    public function setDefaultSourceCode(string $defaultSourceCode): self
    {
        $this->defaultSourceCode = $defaultSourceCode;
        return $this;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    protected function updateSource(\Doctrine\Persistence\Event\LifecycleEventArgs $args) : void
    {
        $entity = $args->getObject();

        if (! $entity instanceof HasSourceInterface) {
            return;
        }
        if ($entity->getSource() === null) {
            $defaultSource = $this->fetchDefaultSource($args->getObjectManager());
            $entity->setSource($defaultSource);
        }

        //Si l'entité n'as pas encore de codeSource
        if ($entity->getSourceCode() === null) {
            //Gestion du source Code
            $code = $entity->getCode();
            if($code == null && $entity instanceof HasCodeInterface){
                $code = $entity->generateDefaultCode();
            }
            $code = ($code) ?? uniqid(); // par défaut, on utilise un uid (si on est dans une création, on n'as pas encore d'ID)
            $entity->setSourceCode($code);
        }

    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    private function fetchDefaultSource(EntityManager $entityManager): Source
    {
        if ($this->sourceEntityClass === null) {
            throw new RuntimeException("La classe d'entité Source n'a pas été spécifiée");
        }
        if ($this->defaultSourceCode === null) {
            throw new RuntimeException("Aucun code n'a été spécifié pour la Source par défaut");
        }
        /** @var Source $defaultSource */
        $defaultSource = $entityManager->getRepository($this->sourceEntityClass)->findOneBy(['code' => $this->defaultSourceCode]);
        if ($defaultSource === null) {
            throw new RuntimeException("Source par défaut introuvable avec ce code : " . $this->defaultSourceCode);
        }

        return $defaultSource;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function prePersist(PrePersistEventArgs $args)
    {
        $this->updateSource($args);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->updateSource($args);
    }

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }
}