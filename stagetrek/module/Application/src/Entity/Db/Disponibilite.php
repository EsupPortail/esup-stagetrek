<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use DateTime;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Disponibilite
 */
class Disponibilite implements ResourceInterface
{
    const RESOURCE_ID = 'Disponibilite';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use HasEtudiantTrait;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateFin = null;
    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateDebut = null;

    /**
     * @var string|null
     */
    protected ?string $informationsComplementaires = null;

    /**
     * Get dateDebut.
     *
     * @return DateTime|null
     */
    public function getDateDebut(): ?DateTime
    {
        return $this->dateDebut;
    }

    /**
     * Set dateDebut.
     *
     * @param \DateTime $dateDebut
     *
     * @return Disponibilite
     */
    public function setDateDebut(DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    /**
     * Get dateFin.
     *
     * @return DateTime|null
     */
    public function getDateFin(): ?DateTime
    {
        return $this->dateFin;
    }

    /**
     * Set dateFin.
     *
     * @param \DateTime $dateFin
     *
     * @return Disponibilite
     */
    public function setDateFin(DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }



    public function isActive() : bool
    {
        $today = new DateTime();
        return $this->dateDebut < $today && $today < $this->dateFin;
    }

    /**
     * Get informationsComplementaires.
     *
     * @return string|null
     */
    public function getInformationsComplementaires(): ?string
    {
        return $this->informationsComplementaires;
    }

    /**
     * Set informationsComplementaires.
     *
     * @param string|null $informationsComplementaires
     *
     * @return Disponibilite
     */
    public function setInformationsComplementaires(string $informationsComplementaires = null): static
    {
        $this->informationsComplementaires = $informationsComplementaires;
        return $this;
    }
}
