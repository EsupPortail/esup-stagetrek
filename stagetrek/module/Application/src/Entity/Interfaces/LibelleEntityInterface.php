<?php

namespace Application\Entity\Interfaces;

interface LibelleEntityInterface
{
    public function getLibelle(): ?string;
    public function setLibelle(string $libelle): static;
}