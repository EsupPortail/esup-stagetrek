<?php

namespace Application\Entity\Traits\InterfaceImplementation;

trait HasIdTrait
{

    // !!!! mixed et non int car l'usage d'un int pose des problème avec l'entityManager qui détecte nécessairements des changements
    protected mixed $id=null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return \Application\Entity\Traits\InterfaceImplementation\HasIdTrait
     */
    public function setId(int|string $id): static
    {
        $this->id = $id;
        return $this;
    }
}