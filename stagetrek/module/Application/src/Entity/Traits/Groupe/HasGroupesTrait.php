<?php

namespace Application\Entity\Traits\Groupe;

use Application\Entity\Db\Groupe;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasGroupesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $groupes;

    /**
     * @return void
     */
    protected function initGroupesCollection(): void
    {
        $this->groupes = new ArrayCollection();
    }

    /**
     * @param Groupe $groupe
     * @return \Application\Entity\Traits\HasGroupesTrait
     */
    public function addGroupe(Groupe $groupe) : static
    {
        if(!$this->groupes->contains($groupe)){
            $this->groupes->add($groupe);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param Groupe $groupe
     * @return \Application\Entity\Traits\HasGroupesTrait
     */
    public function removeGroupe(Groupe $groupe) : static
    {
        $this->groupes->removeElement($groupe);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupes() : Collection
    {
        return $this->groupes;
    }
}