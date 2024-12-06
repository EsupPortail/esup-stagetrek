<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Entity\Traits\Stage\HasTerrainStageSecondaireTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Provider\EtatType\AffectationEtatTypeProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;

/**
 * AffectationStage
 */
class AffectationStage implements ResourceInterface,
 HasEtatsInterface
{
    const RESOURCE_ID = 'AffectationStage';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function __construct()
    {
        $this->etats = new ArrayCollection();
    }

    use IdEntityTrait;

    use HasStageTrait;
    use HasTerrainStageTrait;
    use HasTerrainStageSecondaireTrait;
    use HasEtatsTrait;
    /** Fonction annexe de simplification */
    public function getSessionStage() : ?SessionStage
    {
        return ($this->hasStage()) ? $this->stage->getSessionStage() : null;
    }
    public function getEtudiant() : ?Etudiant
    {
        return ($this->hasStage()) ? $this->stage->getEtudiant() : null;
    }


    /// Gestions des couts
    /**
     * @var float|null
     */
    protected ?float $cout = 0;
    /**
     * @var float|null
     */
    protected ?float $coutTerrain = 0;
    /**
     * @var float|null
     */
    protected ?float $bonusMalus = 0;

    /**
     * @var string|null
     */
    protected ?string $informationsComplementaires = null;

    /**
     * @var bool
     */
    protected bool $preValidee = false;

    /**
     * @var bool
     */
    protected bool $validee = false;
    /**
     * Get scorePreProcedure.
     *
     * @return float|null
     */
    public function getScorePreAffectation(): ?float
    {
        return $this->stage->getScorePreAffectation();
    }

    /**
     * Get scorePreProcedure.
     *
     * @return float|null
     */
    public function getScorePostAffectation(): ?float
    {
        return $this->stage->getScorePostAffectation();
    }
    /**
     * Get scorePreProcedure.
     *
     * @return int|null
     */
    public function getOrdreAffectation(): ?int
    {
        return $this->stage->getOrdreAffectation();
    }

    /**
     * Set cout.
     *
     * @param float|null $cout
     *
     * @return AffectationStage
     */
    public function setCout(float $cout = null) : static
    {
        $this->cout = $cout;
        return $this;
    }

    /**
     * Get cout.
     *
     * @return float|int|null
     */
    public function getCout(): float|int|null
    {
        return $this->cout;
    }

    /**
     * Set coutTerrain.
     *
     * @param float|null $coutTerrain
     *
     * @return AffectationStage
     */
    public function setCoutTerrain(float $coutTerrain = null) : static
    {
        $this->coutTerrain = $coutTerrain;
        return $this;
    }

    /**
     * Get coutTerrain.
     *
     * @return float|int|null
     */
    public function getCoutTerrain(): float|int|null
    {
        return $this->coutTerrain;
    }


    /**
     * Set bonusMalus.
     *
     * @param float|null $bonusMalus
     *
     * @return AffectationStage
     */
    public function setBonusMalus(float $bonusMalus = null) : static
    {
        $this->bonusMalus = $bonusMalus;
        return $this;
    }

    /**
     * Get bonusMalus.
     *
     * @return float|int|null
     */
    public function getBonusMalus(): float|int|null
    {
        return $this->bonusMalus;
    }

    /**
     * Set informationsComplementaires.
     *
     * @param string|null $informationsComplementaires
     *
     * @return AffectationStage
     */
    public function setInformationsComplementaires(string $informationsComplementaires = null) : static
    {
        $this->informationsComplementaires = $informationsComplementaires;

        return $this;
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
     * Set preValidee.
     *
     * @param bool $preValidee
     *
     * @return AffectationStage
     */
    public function setPreValidee(bool $preValidee): static
    {
        $this->preValidee = $preValidee;

        return $this;
    }

    /**
     * Get preValidee.
     *
     * @return bool|string
     */
    public function getPreValidee(): bool|string
    {
        return $this->preValidee;
    }
    public function isPreValidee(): bool|string
    {
        return $this->preValidee;
    }

    /**
     * Set validee.
     *
     * @param bool $validee
     *
     * @return AffectationStage
     */
    public function setValidee(bool $validee): static
    {
        $this->validee = $validee;

        return $this;
    }

    /**
     * Get validee.
     *
     * @return bool|string
     */
    public function getValidee(): bool|string
    {
        return $this->validee;
    }
    public function isValidee(): bool|string
    {
        return $this->validee;
    }

    /**
     * TODO : a revoir
     * @return boolean
     */
    public function isVisibleByEtudiant(): bool
    {
        $sessionVisible = $this->getSessionStage()->isVisibleByEtudiants();
        $stageVisible = $this->getStage()->isVisibleByEtudiant();
        $dateValide = ($this->getStage()->getDateFinCommission() <= new DateTime());
       return $sessionVisible && $stageVisible && $dateValide
           && ($this->hasEtatValidee()
               || $this->hasEtatStageNonEffectue()
               || $this->hasEtatEnDisponibilite()
           )
           ;
    }

    /**
     * @var int|null
     */
    protected ?int $rangPreference = null;
    /**
     * Get rangPreference.
     *
     * @return int|null
     */
    public function getRangPreference(): ?int
    {
        return $this->rangPreference;
    }
    public function setRangPreference(?int $rang): static
    {
        $this->rangPreference = $rang;
        return $this;
    }

    public function hasPreferencesNonRepresentative() : bool
    {
        return ($this->stage->getDateCommission() < new DateTime())
            && !$this->stage->getPreferences()->isEmpty()
            && (!isset($this->rangPreference) || $this->rangPreference==0);
    }

    public function hasNoPreferences() : bool
    {
        return $this->stage->getPreferences()->isEmpty();
    }

    /**
     * @return boolean
     */
    public function hasEtatFutur(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::FUTUR);
    }

    public function hasEtatEnCours(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::EN_COURS);
    }

    public function hasEtatProposition(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::PROPOSTION);
    }
    public function hasEtatPreValidee(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::PRE_VAlIDEE);
    }
    public function hasEtatValidee(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::VAlIDEE);
    }

    public function hasEtatEnRetard(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::EN_RETARD);
    }

    public function hasEtatEnAlerte(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::EN_AlERTE);
    }

    public function hasEtatEnErreur(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::EN_ERREUR);
    }

    public function hasEtatEnDisponibilite(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::EN_DISPO);
    }

    public function hasEtatStageNonEffectue(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::STAGE_NON_EFFECTUE);
    }
    public function hasEtatNonAffecte(): bool
    {
        return $this->isEtatActif(AffectationEtatTypeProvider::NON_AFFECTE);
    }

}
