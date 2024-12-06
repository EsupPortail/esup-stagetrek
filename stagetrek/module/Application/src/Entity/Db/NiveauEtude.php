<?php

namespace Application\Entity\Db;

use Application\Entity\Db;
use Application\Entity\Traits\Groupe\HasGroupesTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * NiveauEtude
 */
class NiveauEtude implements ResourceInterface
{
    const RESOURCE_ID = 'NiveauEtude';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
    use HasGroupesTrait;

    /**
     * @var int|null
     */
    protected ?int $nbStages = 1;

    /**
     * @var \Application\Entity\Db\NiveauEtude|null
     */
    protected ?NiveauEtude $niveauEtudeParent = null;

    /**
     * @var \Application\Entity\Db\NiveauEtude|null
     */
    protected ?NiveauEtude $niveauEtudeSuivant = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $terrainsContraints;

    /**
     * @var bool|null
     */
    protected ?bool $active = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initGroupesCollection();
        $this->terrainsContraints = new ArrayCollection();
    }

    /**
     * Get nbStages.
     *
     * @return int|null
     */
    public function getNbStages(): ?int
    {
        return $this->nbStages;
    }

    /**
     * Set nbStages.
     *
     * @param int|null $nbStages
     *
     * @return NiveauEtude
     */
    public function setNbStages(?int $nbStages): static
    {
        $this->nbStages = $nbStages;
        return $this;
    }

    /**
     * Get niveauEtudeParent.
     *
     * @return NiveauEtude|null
     */
    public function getNiveauEtudeParent(): ?NiveauEtude
    {
        return $this->niveauEtudeParent;
    }

    /**
     * Set niveauEtudeParent.
     *
     * @param NiveauEtude|null $niveauEtudeParent
     *
     * @return NiveauEtude
     */
    public function setNiveauEtudeParent(?NiveauEtude $niveauEtudeParent): static
    {
        $this->niveauEtudeParent = $niveauEtudeParent;
        return $this;
    }

    /**
     * Get niveauEtudeSuivant.
     *
     * @return NiveauEtude|null
     */
    public function getNiveauEtudeSuivant(): ?NiveauEtude
    {
        return $this->niveauEtudeSuivant;
    }

    /**
     * Set niveauEtudeSuivant.
     *
     * @param NiveauEtude|null $niveauEtudeSuivant
     *
     * @return NiveauEtude
     */
    public function setNiveauEtudeSuivant(?NiveauEtude $niveauEtudeSuivant): static
    {
        $this->niveauEtudeSuivant = $niveauEtudeSuivant;
        return $this;
    }

    /**
     * Get active.
     *
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * Set active.
     *
     * @param bool|null $active
     *
     * @return NiveauEtude
     */
    public function setActive(?bool $active): static
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Add terrainsContraint.
     *
     * @param \Application\Entity\Db\TerrainStage $terrainsContraint
     *
     * @return NiveauEtude
     */
    public function addTerrainsContraint(TerrainStage $terrainsContraint): static
    {
        $this->terrainsContraints[] = $terrainsContraint;
        return $this;
    }

    /**
     * Remove terrainsContraint.
     *
     * @param \Application\Entity\Db\TerrainStage $terrainsContraint
     * @return \Application\Entity\Db\NiveauEtude
     */
    public function removeTerrainsContraint(Db\TerrainStage $terrainsContraint) : static
    {
        $this->terrainsContraints->removeElement($terrainsContraint);
        return $this;
    }

    /**
     * Get terrainsContraints.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerrainsContraints(): Collection
    {
        return $this->terrainsContraints;
    }
}
