<?php

namespace Application\Validator\Actions\Traits;

use Application\Validator\Actions\StageValidator;

Trait StageValidatorAwareTrait
{
    /** @var StageValidator|null $stageValidator */
    protected ?StageValidator $stageValidator = null;

    public function getStageValidator(): ?StageValidator
    {
        return $this->stageValidator;
    }

    public function setStageValidator(?StageValidator $stageValidator): static
    {
        $this->stageValidator = $stageValidator;
        return $this;
    }



}