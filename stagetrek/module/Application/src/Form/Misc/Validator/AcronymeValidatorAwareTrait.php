<?php


namespace Application\Form\Misc\Validator;


trait AcronymeValidatorAwareTrait
{
    /** @var AcronymeValidator|null $acronymeValidator */
    protected ?AcronymeValidator $acronymeValidator = null;

    /**
     * @return  AcronymeValidator|null
     */
    public function getAcronymeValidator() : ?AcronymeValidator
    {
        return $this->acronymeValidator;
    }

    /**
     * @param AcronymeValidator $acronymeValidator
     * @return \Application\Form\Misc\Validator\AcronymeValidatorAwareTrait
     */
    public function setAcronymeValidator(AcronymeValidator $acronymeValidator) : static
    {
        $this->acronymeValidator = $acronymeValidator;
        return $this;
    }
}