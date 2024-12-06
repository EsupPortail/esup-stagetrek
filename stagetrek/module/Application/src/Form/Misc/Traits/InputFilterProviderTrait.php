<?php

namespace Application\Form\Misc\Traits;


/**
 * Trait permettant d'accéder a des fonctions de base liée au input filer
 */

trait InputFilterProviderTrait
{
    /** @var array $inputFilterSpecification */
    protected array $inputFilterSpecification = [];

    /**
     * @return array
     */
    public function getInputFilterSpecification(): array
    {
        return $this->inputFilterSpecification;
    }

    public function setInputfilterSpecification(string $inputId, array $specification) : void
    {
        $this->inputFilterSpecification[$inputId] = $specification;
    }
}