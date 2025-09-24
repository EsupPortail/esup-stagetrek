<?php

namespace Application\Entity\Interfaces;

//Méthodes par défaut pour les entités ayant un code
//Permet également de faire un liens avec les validateur de formulaire
interface HasAcronymeInterface
{
    public function getAcronyme(): ?string;
    public function setAcronyme(?string $code): static;
}