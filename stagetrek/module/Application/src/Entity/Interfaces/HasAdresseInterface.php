<?php

namespace  Application\Entity\Interfaces;
use Application\Entity\Db\Adresse;

interface HasAdresseInterface
{
    /**
     * @return \Application\Entity\Db\Adresse|null
     */
    public function getAdresse(): ?Adresse;
    public function setAdresse(?Adresse $adresse): static;
    public function hasAdresse(): bool;
}