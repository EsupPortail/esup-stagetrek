<?php

namespace Application\Entity\Traits\Convention;

use Application\Entity\Db\ConventionStageSignataire;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait HasConventionStageSignatairesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $conventionStageSignataires;

    /**
     * @return void
     */
    protected function initConventionStageSignatairesCollection(): void
    {
        $this->conventionStageSignataires = new ArrayCollection();
    }

    /**
     * @param ConventionStageSignataire $conventionStageSignataires
     * @return HasConventionStageSignatairesTrait
     */
    public function addConventionStageSignataire(ConventionStageSignataire $conventionStageSignataires) : static
    {
        if(!$this->conventionStageSignataires->contains($conventionStageSignataires)){
            $this->conventionStageSignataires->add($conventionStageSignataires);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param ConventionStageSignataire $conventionStageSignataires
     * @return HasConventionStageSignatairesTrait
     */
    public function removeConventionStageSignataire(ConventionStageSignataire $conventionStageSignataires) : static
    {
        $this->conventionStageSignataires->removeElement($conventionStageSignataires);
        return $this;
    }
    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConventionStageSignataires() : Collection
    {
        return $this->conventionStageSignataires;
    }

}