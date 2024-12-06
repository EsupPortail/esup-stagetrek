<?php


namespace Application\Form\Misc\Validator;


trait LibelleValidatorAwareTrait
{
    /** @var LibelleValidator|null $libelleValidator */
    protected ?LibelleValidator $libelleValidator = null;

    /**
     * @return \Application\Form\Misc\Validator\LibelleValidator|null
     */
    public function getLibelleValidator() : ?LibelleValidator
    {
        return $this->libelleValidator;
    }

    /**
     * @param LibelleValidator|null $libelleValidator
     * @return \Application\Form\Misc\Validator\LibelleValidatorAwareTrait
     */
    public function setLibelleValidator(?LibelleValidator $libelleValidator) : static
    {
        $this->libelleValidator = $libelleValidator;
        return $this;
    }
}