<?php

namespace Application\Entity\Traits\Etudiant;

use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasDisponibilitesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $disponibilites;

    /**
     * @param Disponibilite $disponibilites
     * @return \Application\Entity\Traits\HasDisponibilitesTrait
     */
    public function addDisponibilite(Disponibilite $disponibilites): static
    {
        if (!$this->disponibilites->contains($disponibilites)) {
            $this->disponibilites->add($disponibilites);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param Disponibilite $disponibilites
     * @return \Application\Entity\Traits\HasDisponibilitesTrait
     */
    public function removeDisponibilite(Disponibilite $disponibilites): static
    {
        $this->disponibilites->removeElement($disponibilites);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDisponibilites(): Collection
    {
        return $this->disponibilites;
    }

    /**
     * @return void
     */
    protected function initDisponibilitesCollection(): void
    {
        $this->disponibilites = new ArrayCollection();
    }

    public function isEnDispo() : bool
    {
        //fonction valable uniquement pour les étudiants mais mise ici pour simplifier le code
        if(!$this instanceof Etudiant){return false;}
        if($this->disponibilites->isEmpty()){return false;}
        /** @var Disponibilite $dispo */
        foreach ($this->getDisponibilites() as $dispo){
            if($dispo->isActive()){return true;}
        }
        return false;
    }
}