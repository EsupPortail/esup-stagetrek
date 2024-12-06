<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\ValidationStage;

/**
 *
 */
trait HasValidationStageTrait
{
    /**
     * @var \Application\Entity\Db\ValidationStage|null
     */
    protected ?ValidationStage $validationStage;

    /**
     * @return \Application\Entity\Db\ValidationStage|null
     */
    public function getValidationStage(): ?ValidationStage
    {
        return $this->validationStage;
    }

    /**
     * @param \Application\Entity\Db\ValidationStage|null $validationStage
     * @return \Application\Entity\Traits\HasValidationStageTrait
     */
    public function setValidationStage(?ValidationStage $validationStage): static
    {
        $this->validationStage = $validationStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasValidationStage(): bool
    {
        return $this->validationStage !== null;
    }
}