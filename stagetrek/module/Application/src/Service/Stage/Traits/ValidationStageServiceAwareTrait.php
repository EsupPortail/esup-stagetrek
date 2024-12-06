<?php

namespace Application\Service\Stage\Traits;

use Application\Service\Stage\ValidationStageService;

/**
 * Traits StageServiceAwareTrait
 * @package Application\Service\Stage
 */
Trait ValidationStageServiceAwareTrait
{
    /** @var ValidationStageService|null $validationStageService */
    protected ?ValidationStageService $validationStageService = null;

    /**
     * @return ValidationStageService
     */
    public function getValidationStageService(): ValidationStageService
    {
        return $this->validationStageService;
    }

    /**
     * @param ValidationStageService $validationStageService
     * @return \Application\Service\Stage\Traits\ValidationStageServiceAwareTrait
     */
    public function setValidationStageService(ValidationStageService $validationStageService): static
    {
        $this->validationStageService = $validationStageService;
        return $this;
    }


}