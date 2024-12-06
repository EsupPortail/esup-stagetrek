<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\AcronymeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Traits\Contraintes\HasContrainteCursusPorteeTrait;
use Application\Entity\Traits\InterfaceImplementation\AcronymeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\DescriptionEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Entity\Traits\Terrain\HasCategorieStageTrait;
use DateTime;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ContrainteCursus
 */
class ContrainteCursus implements ResourceInterface,
    LibelleEntityInterface, AcronymeEntityInterface
{
    const RESOURCE_ID = 'ContrainteCursus';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use LibelleEntityTrait;
    use AcronymeEntityTrait;

    use HasContrainteCursusPorteeTrait;
    use HasCategorieStageTrait;
    use HasTerrainStageTrait;
    use OrderEntityTrait;
    use DescriptionEntityTrait;

    /**
     * @var int|null
     */
    protected ?int $nombreDeStageMin = null;
    /**
     * @var int|null
     */
    protected ?int $nombreDeStageMax = null;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateDebut = null;
    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateFin = null;

    /**
     * @var bool|null
     */
    protected ?bool $isContradictoire = false;

    /**
     * Constructor
     * @throws \DateMalformedStringException
     */
    public function __construct()
    {
        //Date par défaut
        $yearStar = date('Y');
        if (date('m') > 9) {
            $yearStar++;
        }
        $this->dateDebut = new DateTime('01-09-' . $yearStar);
        $this->dateDebut->setTime(0, 0);
        $this->dateFin = new DateTime('31-08-' . ($yearStar + 3));
        $this->dateFin->setTime(23, 59, 59);
    }

    /**
     * Get nombreDeStageMin.
     *
     * @return int|null
     */
    public function getNombreDeStageMin(): ?int
    {
        return $this->nombreDeStageMin;
    }

    /**
     * Set nombreDeStageMin.
     *
     * @param int|null $nombreDeStageMin
     *
     * @return ContrainteCursus
     */
    public function setNombreDeStageMin(int $nombreDeStageMin = null): static
    {
        $this->nombreDeStageMin = $nombreDeStageMin;
        return $this;
    }

    /**
     * Get nombreDeStageMax.
     *
     * @return int|null
     */
    public function getNombreDeStageMax(): ?int
    {
        return $this->nombreDeStageMax;
    }

    /**
     * Set nombreDeStageMax.
     *
     * @param int|null $nombreDeStageMax
     *
     * @return ContrainteCursus
     */
    public function setNombreDeStageMax(int $nombreDeStageMax = null): static
    {
        $this->nombreDeStageMax = $nombreDeStageMax;
        return $this;
    }

    public function hasPorteeGeneral(): bool
    {
        return $this->hasContrainteCursusPortee() && $this->getContrainteCursusPortee()->isType(ContrainteCursusPortee::ID_PORTEE_GENERAL);
    }

    public function hasPorteeCategorie(): bool
    {
        return $this->hasContrainteCursusPortee() && $this->getContrainteCursusPortee()->isType(ContrainteCursusPortee::ID_PORTEE_CATEGORIE);
    }

    public function hasPorteeTerrain(): bool
    {
        return $this->hasContrainteCursusPortee() && $this->getContrainteCursusPortee()->isType(ContrainteCursusPortee::ID_PORTEE_TERRAIN);
    }

    /**
     * Get categorieStage.
     *
     * @return \Application\Entity\Db\CategorieStage|null
     */
    public function getCategorieStage(): ?CategorieStage
    {
        if(!$this->hasCategorieStage() && $this->hasTerrainStage()){
            return $this->getTerrainStage()->getCategorieStage();
        }
        return $this->categorieStage;
    }

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
     * @return ContrainteCursus
     */
    public function setDateDebut(DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    /**
     * Get dateFin.
     *
     * @return \DateTime|null
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
     * @return ContrainteCursus
     */
    public function setDateFin(DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    /**
     * Get isContradictoire.
     *
     * @return bool|null
     */
    public function getIsContradictoire(): ?bool
    {
        return $this->isContradictoire;
    }

    /**
     * Set isContradictoire.
     *
     * @param bool|null $isContradictoire
     *
     * @return ContrainteCursus
     */
    public function setIsContradictoire(?bool $isContradictoire = null): static
    {
        $this->isContradictoire = $isContradictoire;
        return $this;
    }

    /**
     * Get isContradictoire.
     *
     * @return bool|null
     */
    public function isContradictoire(): ?bool
    {
        return $this->isContradictoire;
    }
}
