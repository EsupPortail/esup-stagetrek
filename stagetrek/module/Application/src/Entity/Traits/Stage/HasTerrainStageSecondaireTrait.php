<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\TerrainStage;
use Exception;

/**
 *
 */
trait HasTerrainStageSecondaireTrait
{
    /**
     * @var \Application\Entity\Db\TerrainStage|null
     */
    protected ?TerrainStage $terrainStageSecondaire = null;

    /**
     * @return \Application\Entity\Db\TerrainStage|null
     */
    public function getTerrainStageSecondaire(): ?TerrainStage
    {
        return $this->terrainStageSecondaire;
    }

    /**
     * @param \Application\Entity\Db\TerrainStage|null $terrainSecondaire
     * @return \Application\Entity\Traits\Stage\HasTerrainStageSecondaireTrait
     * @throws \Exception
     */
    public function setTerrainStageSecondaire(?TerrainStage $terrainSecondaire): static
    {
        if(isset($terrainSecondaire) && ! $terrainSecondaire->isTerrainSecondaire()){
            throw new Exception("Le terrain de stage n'est pas classer comme secondaire");
        }
        $this->terrainStageSecondaire = $terrainSecondaire;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasTerrainStageSecondaire(): bool
    {
        return $this->terrainStageSecondaire !== null;
    }

}