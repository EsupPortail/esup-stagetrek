<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * SessionStageTerrainLinker
 */
class SessionStageTerrainLinker implements ResourceInterface
{
    const RESOURCE_ID = "SessionStageTerrainLinker";
    /**
     * @return string
     */
    public function getResourceId() : string
    {
        return self::RESOURCE_ID;
    }

    use HasIdTrait;

    /**
     * @var int
     */
    protected int $nbPlacesOuvertes = 0;

    /**
     * @var int
     */
    protected int $nbPlacesRecommandees = 0;

    use HasSessionStageTrait;
    use HasTerrainStageTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set nbPlaceOuverte.
     *
     * @param int $nbPlacesOuvertes
     *
     * @return SessionStageTerrainLinker
     */
    public function setNbPlacesOuvertes(int $nbPlacesOuvertes): static
    {
        $this->nbPlacesOuvertes = $nbPlacesOuvertes;

        return $this;
    }

    /**
     * Get nbPlaceOuverte.
     *
     * @return int
     */
    public function getNbPlacesOuvertes() : int
    {
        return $this->nbPlacesOuvertes;
    }

    /**
     * Set nbPlaceRecommande.
     *
     * @param int $nbPlacesRecommandees
     *
     * @return SessionStageTerrainLinker
     */
    public function setNbPlacesRecommandees(int $nbPlacesRecommandees = 0) : static

    {
        $this->nbPlacesRecommandees = $nbPlacesRecommandees;

        return $this;
    }

    /**
     * Get nbPlaceRecommande.
     *
     * @return int
     */
    public function getNbPlacesRecommandees() : int
    {
        return $this->nbPlacesRecommandees;
    }


    /**
     * @var int
     */
    private int $nbPlacesDisponibles = 0;

    /** @var \Application\Entity\Db\TerrainStageNiveauDemande|null $niveauDemande */
    protected ?TerrainStageNiveauDemande $niveauDemande = null;
    public function getNiveauDemande(): ?TerrainStageNiveauDemande
    {
        return $this->niveauDemande;
    }
    public function setNiveauDemande(?TerrainStageNiveauDemande $niveauDemande): void
    {
        $this->niveauDemande = $niveauDemande;
    }

    //TIDI ; a supprimer

    const DEGRE_DEMANDE_TERRAIN_FERMEE = -1;
    const DEGRE_DEMANDE_TERRAIN_NUL = 0;
    const DEGRE_DEMANDE_TERRAIN_TRES_FAIBLE = 1;
    const DEGRE_DEMANDE_TERRAIN_FAIBLE = 2;
    const DEGRE_DEMANDE_TERRAIN_MOYENNE = 3;
    const DEGRE_DEMANDE_TERRAIN_FORTE = 4;
    const DEGRE_DEMANDE_TERRAIN_TRES_FORTE = 5;

    /**
     * @var int|null
     */
    private ?int $degreDemande = self::DEGRE_DEMANDE_TERRAIN_NUL;

    /**
     * @var string|null
     */
    private ?string $libelleDemande = null ;

    /**
     * Set nbPlacesDisponibles.
     *
     * @param int $nbPlacesDisponibles
     *
     * @return SessionStageTerrainLinker
     */
    public function setNbPlacesDisponibles(int $nbPlacesDisponibles) : static
    {
        $this->nbPlacesDisponibles = $nbPlacesDisponibles;

        return $this;
    }

    /**
     * Get nbPlacesDisponible.
     *
     * @return int
     */
    public function getNbPlacesDisponibles() : int
    {
        return $this->nbPlacesDisponibles;
    }


    /**
     * @var int|null
     */
    private ?int $nbPlacesAffectees = 0;


    /**
     * Set nbPlacesAffectees.
     *
     * @param int $nbPlacesAffectees
     *
     * @return SessionStageTerrainLinker
     */
    public function setNbPlacesAffectees(int $nbPlacesAffectees): static
    {
        $this->nbPlacesAffectees = $nbPlacesAffectees;

        return $this;
    }

    /**
     * Get nbPlacesAffectees.
     *
     * @return int|null
     */
    public function getNbPlacesAffectees(): ?int
    {
        return $this->nbPlacesAffectees;
    }


    /**
     * @var int|null
     */
    private ?int $nbPlacesPreAffectees = 0;

    /**
     * Set nbPlacesAffectees.
     *
     * @param int $nbPlacesPreAffectees
     *
     * @return SessionStageTerrainLinker
     */
    public function setNbPlacesPreAffectees(int $nbPlacesPreAffectees): static
    {
        $this->nbPlacesPreAffectees = $nbPlacesPreAffectees;

        return $this;
    }

    /**
     * Get nbPlacesPreAffectees.
     *
     * @return int|null
     */
    public function getNbPlacesPreAffectees(): ?int
    {
        return $this->nbPlacesPreAffectees;
    }
}
