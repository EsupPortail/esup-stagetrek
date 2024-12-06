<?php

namespace Application\Service\TerrainStage\Traits;

use Application\Service\TerrainStage\CategorieStageService;

/**
 * Traits CategorieStageServiceAwareTrait
 * @package Application\Service\TerrainStage
 */
trait CategorieStageServiceAwareTrait
{
    /** @var CategorieStageService|null $categorieStageService */
    protected ?CategorieStageService $categorieStageService = null;

    /**
     * @return CategorieStageService
     */
    public function getCategorieStageService(): CategorieStageService
    {
        return $this->categorieStageService;
    }

    /**
     * @param CategorieStageService $categorieStageService
     * @return \Application\Service\TerrainStage\Traits\CategorieStageServiceAwareTrait
     */
    public function setCategorieStageService(CategorieStageService $categorieStageService): static
    {
        $this->categorieStageService = $categorieStageService;
        return $this;
    }
}