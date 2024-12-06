<?php

namespace Application\Validator\Actions\Traits;

use Application\Validator\Actions\ValidationStageValidator;

Trait ValidationStageValidatorAwareTrait
{
    /** @var ValidationStageValidator|null $validationStageValidator */
    protected ?ValidationStageValidator $validationStageValidator = null;

    public function getValidationStageValidator(): ?ValidationStageValidator
    {
        return $this->validationStageValidator;
    }

    public function setValidationStageValidator(?ValidationStageValidator $validationStageValidator): static
    {
        $this->validationStageValidator = $validationStageValidator;
        return $this;
    }
}