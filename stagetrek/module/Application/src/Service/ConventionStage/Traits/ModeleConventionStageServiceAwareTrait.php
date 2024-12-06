<?php

namespace Application\Service\ConventionStage\Traits;

use Application\Service\ConventionStage\ModeleConventionStageService;

/**
 * Traits ConventionStageServiceAwareTrait
 * @package Application\Service\ConventionStage
 */
Trait ModeleConventionStageServiceAwareTrait
{

    /** @var ModeleConventionStageService|null $modeleConventionStageService */
    protected ?ModeleConventionStageService $modeleConventionStageService = null;

    /**
     * @return ModeleConventionStageService
     */
    public function getModeleConventionStageService(): ModeleConventionStageService
    {
        return $this->modeleConventionStageService;
    }

    /**
     * @param ModeleConventionStageService $modeleConventionStageService
     * @return \Application\Service\ConventionStage\Traits\ModeleConventionStageServiceAwareTrait
     */
    public function setModeleConventionStageService(ModeleConventionStageService $modeleConventionStageService): static
    {
        $this->modeleConventionStageService = $modeleConventionStageService;
        return $this;
    }
}