<?php

namespace Application\Entity\Traits\Contrainte;

use Application\Entity\Db\ContrainteCursusEtudiant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasContraintesCursusEtudiantsTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $contraintesCursusEtudiants;

    /**
     * @param ContrainteCursusEtudiant $contraintesCursusEtudiants
     * @return \Application\Entity\Traits\Contrainte\HasContraintesCursusEtudiantsTrait
     */
    public function addContraintesCursusEtudiants(ContrainteCursusEtudiant $contraintesCursusEtudiants): static
    {
        if (!$this->contraintesCursusEtudiants->contains($contraintesCursusEtudiants)) {
            $this->contraintesCursusEtudiants->add($contraintesCursusEtudiants);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param ContrainteCursusEtudiant $contraintesCursusEtudiants
     * @return \Application\Entity\Traits\Contrainte\HasContraintesCursusEtudiantsTrait
     */
    public function removeContraintesCursusEtudiants(ContrainteCursusEtudiant $contraintesCursusEtudiants): static
    {
        $this->contraintesCursusEtudiants->removeElement($contraintesCursusEtudiants);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContraintesCursusEtudiants(): Collection
    {
        return $this->contraintesCursusEtudiants;
    }

    /**
     * @return void
     */
    protected function initContraintesCursusEtudiants(): void
    {
        $this->contraintesCursusEtudiants = new ArrayCollection();
    }
}