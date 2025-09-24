<?php

namespace Application\Service\Referentiel\Interfaces;

use DateTime;

interface ReferentielEtudiantInterface
{
    /** @return string */
    public function getSource(): string;

    /** @return string */
    public function getId(): string;

    /** @return string */
    public function getUsername(): string;

    /** @return string */
    public function getDisplayName(): string;

    /** @return string */
    public function getMail(): string;

    /** @return string */
    public function getNumEtu(): string;

    /** @return string */
    public function getLastName(): string;

    /** @return string */
    public function getFirstName(): string;

    /**
     * @desc formatage attendu pour les résult : DateTime
     * @return DateTime|null
     */
    public function getDateNaissance(): ?DateTime;

}