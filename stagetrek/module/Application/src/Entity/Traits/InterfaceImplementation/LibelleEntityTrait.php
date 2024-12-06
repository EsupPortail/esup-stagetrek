<?php

namespace Application\Entity\Traits\InterfaceImplementation;

trait LibelleEntityTrait
{
    /** @var string|null $libelle */
    protected ?string $libelle=null;

    /**
     * Par défaut une entité n'ayant pas de code aura un code #Id
     * @return string|null
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * @param string|null $libelle
     * @return \Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait
     */
    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }
}