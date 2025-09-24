<?php

namespace Application\Entity\Traits\Etudiant;

use Application\Entity\Db\Etudiant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasEtudiantsTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection|null
     */
    protected ?Collection $etudiants = null;

    /**
     * @param Etudiant $etudiant
     * @return \Application\Entity\Traits\HasEtudiantsTrait
     */
    public function addEtudiant(Etudiant $etudiant): static
    {
        if(!isset($this->etudiants)){$this->initEtudiantsCollection();}
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param Etudiant $etudiants
     * @return \Application\Entity\Traits\HasEtudiantsTrait
     */
    public function removeEtudiant(Etudiant $etudiant): static
    {
        if(!isset($this->etudiants)){$this->initEtudiantsCollection();}
        $this->etudiants->removeElement($etudiant);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtudiants(): Collection
    {
        if(!isset($this->etudiants)){$this->initEtudiantsCollection();}
        return $this->etudiants;
    }

    public function setEtudiants(Collection|array $etudiants): void
    {
        if(is_array($etudiants)) {
            $etudiants = new ArrayCollection($etudiants);
        }
        if(!isset($this->etudiants)){$this->initEtudiantsCollection();}
        $this->etudiants = $etudiants;
    }


    /**
     * @return void
     */
    protected function initEtudiantsCollection(): void
    {
        $this->etudiants = new ArrayCollection();
    }
}