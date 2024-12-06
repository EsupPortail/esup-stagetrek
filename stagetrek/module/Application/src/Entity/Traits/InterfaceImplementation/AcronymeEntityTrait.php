<?php

namespace Application\Entity\Traits\InterfaceImplementation;

trait AcronymeEntityTrait
{
    /** @var string|null $acronyme */
    protected ?string $acronyme=null;

    /**
     * Par défaut une entité n'ayant pas de code aura un code #Id
     * @return string|null
     */
    public function getAcronyme(): ?string
    {
        return $this->acronyme;
    }

    /**
     * @param string|null $acronyme
     * @return \Application\Entity\Traits\InterfaceImplementation\AcronymeEntityTrait
     */
    public function setAcronyme(?string $acronyme): static
    {
        $this->acronyme = $acronyme;
        return $this;
    }
}