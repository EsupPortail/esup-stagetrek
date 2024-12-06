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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $etudiants;

    /**
     * @param Etudiant $etudiant
     * @return \Application\Entity\Traits\HasEtudiantsTrait
     */
    public function addEtudiant(Etudiant $etudiant): static
    {
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
        $this->etudiants->removeElement($etudiant);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    /**
     * @return void
     */
    protected function initEtudiantsCollection(): void
    {
        $this->etudiants = new ArrayCollection();
    }
}