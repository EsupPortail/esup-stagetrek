<?php

namespace Application\Entity\Interfaces;

interface HasLibelleInterface
{
    public function getLibelle(): ?string;
    public function setLibelle(string $libelle): static;
}