<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\Convention\HasConventionStageSignatairesTrait;
use Application\Entity\Traits\Convention\HasModeleConventionStageTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use DateTime;
use UnicaenFichier\Entity\Db\Fichier;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenRenderer\Entity\Db\Rendu;
/**
 * ConventionStage
 */
class ConventionStage implements ResourceInterface
{
    const RESOURCE_ID = 'ConventionStage';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;

    use HasModeleConventionStageTrait;
    use HasConventionStageSignatairesTrait;
    use HasStageTrait;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateUpdate = null;

    /**
     * @var \UnicaenRenderer\Entity\Db\Rendu|null
     */
    protected ?Rendu $rendu = null;

    protected ?Fichier $fichier = null;
    public function getFichier(): ?Fichier
    {
        return $this->fichier;
    }

    public function setFichier(?Fichier $fichier): void
    {
        $this->fichier = $fichier;
    }

    public function __construct()
    {
        $this->initConventionStageSignatairesCollection();
    }

    /**
     * Get dateUpdate.
     *
     * @return \DateTime|null
     */
    public function getDateUpdate(): ?DateTime
    {
        return $this->dateUpdate;
    }

    /**
     * Set dateUpdate.
     *
     * @param \DateTime $dateUpdate
     *
     * @return ConventionStage
     */
    public function setDateUpdate(DateTime $dateUpdate): static
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }

    /**
     * Set stage.
     *
     * @param \Application\Entity\Db\Stage|null $stage
     *
     * @return ConventionStage
     */
    public function setStage(?Stage $stage): static
    {
        $this->stage = $stage;
        if ($this->hasStage()) {
            $stage->setConventionStage($this);
        }
        if ($this->rendu !== null) {
            $sujet = sprintf("Convention du stage %s - %s", $this->stage->getLibelle(), $this->stage->getEtudiant()->getDisplayName());
            $this->rendu->setSujet($sujet);
        }
        return $this;
    }

    /**
     * Get rendu.
     *
     * @return \UnicaenRenderer\Entity\Db\Rendu|null
     */
    public function getRendu(): ?Rendu
    {
        return $this->rendu;
    }

    /**
     * Set rendu.
     *
     * @param \UnicaenRenderer\Entity\Db\Rendu|null $rendu
     *
     * @return ConventionStage
     */
    public function setRendu(Rendu $rendu = null): static
    {
        $this->rendu = $rendu;
        if ($this->hasRendu() && $this->hasStage()) {
            $sujet = sprintf("Convention du stage %s - %s", $this->stage->getLibelle(), $this->stage->getEtudiant()->getDisplayName());
            $this->rendu->setSujet($sujet);
        }
        return $this;
    }

    public function hasRendu(): bool
    {
        return $this->rendu !== null;
    }

    /**
     * Get rendu.
     *
     * @return string
     */
    public function getCorps(): string
    {
        if ($this->rendu === null) {
            return "";
        }
        return $this->rendu->getCorps();
    }

    public function setCorps(string $corps): static
    {
        if ($this->hasRendu()) {
            $this->rendu->setCorps($corps);
        }
        return $this;
    }

    /**
     * hasFile
     *
     * @return bool
     */
    public function hasFile(): bool
    {
        return $this->fichier !== null;
    }


}
