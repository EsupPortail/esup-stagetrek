<?php

namespace Application\Service\TerrainStage\Traits;

use Application\Service\TerrainStage\TerrainStageService;

/**
 * Traits TerrainStageServiceAwareTrait
 * @package Application\Service\TerrainStage
 */
trait TerrainStageServiceAwareTrait
{
    /** @var ?TerrainStageService $terrainStageService */
    protected ?TerrainStageService $terrainStageService = null;

    /**
     * @return TerrainStageService
     */
    public function getTerrainStageService(): TerrainStageService
    {
        return $this->terrainStageService;
    }

    /**
     * @param TerrainStageService $terrainStageService
     * @return \Application\Service\TerrainStage\Traits\TerrainStageServiceAwareTrait
     */
    public function setTerrainStageService(TerrainStageService $terrainStageService): static
    {
        $this->terrainStageService = $terrainStageService;
        return $this;
    }

}