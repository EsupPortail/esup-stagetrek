<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\TerrainStage;

/**
 *
 */
trait HasTerrainStageTrait
{
    /**
     * @var \Application\Entity\Db\TerrainStage|null
     */
    protected ?TerrainStage $terrainStage = null;

    /**
     * @return \Application\Entity\Db\TerrainStage|null
     */
    public function getTerrainStage(): ?TerrainStage
    {
        return $this->terrainStage;
    }

    /**
     * @param \Application\Entity\Db\TerrainStage|null $terrainStage
     * @return \Application\Entity\Traits\HasTerrainStageTrait
     */
    public function setTerrainStage(?TerrainStage $terrainStage): static
    {
        $this->terrainStage = $terrainStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasTerrainStage(): bool
    {
        return $this->terrainStage !== null;
    }



}