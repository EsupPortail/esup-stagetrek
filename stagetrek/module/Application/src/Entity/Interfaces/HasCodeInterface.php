<?php

namespace Application\Entity\Interfaces;

//Méthodes par défaut pour les entités ayant un code
//Permet également de faire un liens avec les validateur de formulaire
interface HasCodeInterface
{
    public function getCode(): ?string;
    public function setCode(string $code): static;
    public function hasCode(string $code): bool;
    //FOnction qui génrera le code de l'entité
    public function generateDefaultCode(array $param = []) : string;
}