<?php

namespace Application\Service\ConventionStage\Traits;

use Application\Service\ConventionStage\ConventionStageService;

/**
 * Traits ConventionStageServiceAwareTrait
 * @package Application\Service\ConventionStage
 */
trait ConventionStageServiceAwareTrait
{
    /** @var ConventionStageService|null $conventionStageService */
    protected ?ConventionStageService $conventionStageService;

    /**
     * @return ConventionStageService
     */
    public function getConventionStageService(): ConventionStageService
    {
        return $this->conventionStageService;
    }

    /**
     * @param ConventionStageService $conventionStageService
     * @return \Application\Service\ConventionStage\Traits\ConventionStageServiceAwareTrait
     */
    public function setConventionStageService(ConventionStageService $conventionStageService): static
    {
        $this->conventionStageService = $conventionStageService;
        return $this;
    }
}