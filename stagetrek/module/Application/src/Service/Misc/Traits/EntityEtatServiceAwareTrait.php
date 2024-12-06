<?php

namespace Application\Service\Misc\Traits;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Service\EtatInstance\EtatInstanceServiceAwareTrait;
use UnicaenEtat\Service\EtatType\EtatTypeServiceAwareTrait;

/** Permet de mettre a jour l'état d'une entités en lui fournissant uniquement le code de l'état*/
trait EntityEtatServiceAwareTrait
{
    use ProvidesObjectManager;
    use EtatInstanceServiceAwareTrait;
    use EtatTypeServiceAwareTrait;

    /**
     * @throws Exception
     */
    public function setEtatActif(HasEtatsInterface $entity, string $codeEtatType) : HasEtatsInterface
    {
        $etatType = $this->getEtatTypeService()->getEtatTypeByCode($codeEtatType);
        if (!isset($etatType)) {
            throw new Exception("L'état type de code " . $codeEtatType . " n'as pas été trouvé");
        }
        return $this->getEtatInstanceService()->setEtatActif($entity, $etatType->getCode());
    }

    //Met a jour l'état de l'entité

    /**
     * @throws Exception
     */
    public  function updateEtat(HasEtatsInterface $entity): HasEtatsInterface
    {
        $this->getObjectManager()->refresh($entity);
        $this->setEtatInfo(null);
        $codeEtat = $this->computeEtat($entity);
        $this->setEtatActif($entity, $codeEtat);
        $etat = $entity->getEtatActif();
        $etat->setInfos(($this->etatInfo != "")? $this->etatInfo : null);
        $this->getEtatInstanceService()->update($etat);
        $this->getObjectManager()->refresh($entity);
        return $entity;
    }

    /**
     * @throws Exception
     */
    public function updateEtats(array $entities) : void
    {
        foreach ($entities as $entity) {
            $this->updateEtat($entity);
        }
    }

    protected ?string $etatInfo;
    public function getEtatInfo(): ?string
    {
        return $this->etatInfo;
    }

    public function setEtatInfo(?string $etatInfo): void
    {
        $this->etatInfo = $etatInfo;
    }

    public function addEtatInfo(?string $etatInfo): void
    {
        if($this->etatInfo !== null && $this->etatInfo != "" ){
            $this->etatInfo .= "<br/>";
        }
        else{
            $this->etatInfo = "";
        }
        $this->etatInfo .= $etatInfo;
    }


    //Retourne le codeEtat de l'entité
    /**
     * @throws Exception
     */
    protected abstract function computeEtat(HasEtatsInterface $entity) :string;
}