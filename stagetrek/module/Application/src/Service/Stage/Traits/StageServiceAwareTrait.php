<?php

namespace Application\Service\Stage\Traits;

use Application\Service\Stage\StageService;

/**
 * Traits StageServiceAwareTrait
 * @package Application\Service\Stage
 */
Trait StageServiceAwareTrait
{
    /** @var StageService|null $stageService */
    protected ?StageService $stageService = null;
    /**
     * @return StageService
     */
    public function getStageService(): StageService
    {
        return $this->stageService;
    }

    /**
     * @param StageService $stageService
     * @return StageServiceAwareTrait
     */
    public function setStageService(StageService $stageService): static
    {
        $this->stageService = $stageService;
        return $this;
    }

}