<?php

namespace Application\Form\Stages\Traits;
use Application\Form\Stages\ValidationStageForm;

/**
 * Traits ValidationStageFormAwareTrait
 * @package Application\Form\Stages\Traits
 */
trait ValidationStageFormAwareTrait
{
    /**
     * @var ValidationStageForm|null $validationStageForm ;
     */
    protected ?ValidationStageForm $validationStageForm = null;


    public function getValidationStageForm(?bool $modeAdmin = false): ValidationStageForm
    {
        $form = $this->validationStageForm;
        $form->setModeAdmin($modeAdmin);
        return $form;
    }

    /**
     * @param ValidationStageForm $validationStageForm
     * @return \Application\Form\Stages\Traits\ValidationStageFormAwareTrait
     */
    public function setValidationStageForm(ValidationStageForm $validationStageForm): static
    {
        $this->validationStageForm = $validationStageForm;
        return $this;
    }
}