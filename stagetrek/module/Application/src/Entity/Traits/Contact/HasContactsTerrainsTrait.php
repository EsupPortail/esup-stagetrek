<?php

namespace Application\Entity\Traits\Contact;

use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\TerrainStage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasContactsTerrainsTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $contactsTerrains;

    /**
     * @return void
     */
    protected function initContactsTerrainsCollection(): void
    {
        $this->contactsTerrains = new ArrayCollection();
    }

    /**
     * @param ContactTerrain $contactsTerrains
     * @return \Application\Entity\Traits\HasContactsTerrainsTrait
     */
    public function addContactTerrain(ContactTerrain $contactsTerrains) : static
    {
        if(!$this->contactsTerrains->contains($contactsTerrains)){
            $this->contactsTerrains->add($contactsTerrains);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param ContactTerrain $contactsTerrains
     * @return \Application\Entity\Traits\HasContactsTerrainsTrait
     */
    public function removeContactTerrain(ContactTerrain $contactsTerrains) : static
    {
        $this->contactsTerrains->removeElement($contactsTerrains);
        return $this;
    }
    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContactsTerrains() : Collection
    {
        return $this->contactsTerrains;
    }

    public function getContactForTerrain(TerrainStage $terrain) : ?ContactTerrain
    {
        /** @var \Application\Entity\Db\ContactTerrain $ct */
        foreach ($this->getContactsTerrains() as $ct){
            if($ct->getTerrainStage()->getId() == $terrain->getId()){
                return $ct;
            }
        }
        return null;
    }
}