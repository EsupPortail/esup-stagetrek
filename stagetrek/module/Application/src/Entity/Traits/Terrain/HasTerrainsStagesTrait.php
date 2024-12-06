<?php

namespace Application\Entity\Traits\Terrain;

use Application\Entity\Db\TerrainStage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasTerrainsStagesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $terrainsStages;

    /**
     * @return void
     */
    protected function initTerrainsStagesCollection(): void
    {
        $this->terrainsStages = new ArrayCollection();
    }

    /**
     * @param TerrainStage $terrainStage
     * @return \Application\Entity\Traits\Terrain\HasTerrainsStagesTrait
     */
    public function addTerrainStage(TerrainStage $terrainStage) : static
    {
        if(!$this->terrainsStages->contains($terrainStage)){
            $this->terrainsStages->add($terrainStage);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param TerrainStage $terrainStage
     * @return \Application\Entity\Traits\Terrain\HasTerrainsStagesTrait
     */
    public function removeTerrainStage(TerrainStage $terrainStage) : static
    {
        $this->terrainsStages->removeElement($terrainStage);
        return $this;
    }
    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerrainsStages() : Collection
    {
        return $this->terrainsStages;
    }

}