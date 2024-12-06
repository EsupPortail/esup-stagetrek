<?php


namespace Application\Form\Misc\Validator;


trait CodeValidatorAwareTrait
{
    /** @var CodeValidator|null $codeValidator */
    protected ?CodeValidator $codeValidator = null;

    /**
     * @return  CodeValidator
     */
    public function getCodeValidator(): CodeValidator
    {
        return $this->codeValidator;
    }

    /**
     * @param CodeValidator $codeValidator
     * @return \Application\Form\Misc\Validator\CodeValidatorAwareTrait
     */
    public function setCodeValidator(CodeValidator $codeValidator) : static
    {
        $this->codeValidator = $codeValidator;
        return $this;
    }
}