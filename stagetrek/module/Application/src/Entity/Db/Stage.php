<?php

namespace Application\Entity\Db;

use Application\Entity\Db;
use Application\Entity\Traits\Contact\HasContactsStagesTrait;
use Application\Entity\Traits\Convention\HasConventionStageSignatairesTrait;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\Preference\HasPreferencesTrait;
use Application\Entity\Traits\Stage\HasAffectationStageTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Entity\Traits\Stage\HasValidationStageTrait;
use Application\Provider\EtatType\StageEtatTypeProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;
use UnicaenTag\Entity\Db\HasTagsInterface;
use UnicaenTag\Entity\Db\HasTagsTrait;

/**
 * Stage
 */
class Stage implements ResourceInterface, HasEtatsInterface
    , HasTagsInterface
{
    const RESOURCE_ID = 'Stage';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initPreferencesCollection();
        $this->initContactsStagesCollection();
        $this->initConventionStageSignatairesCollection();
        $this->etats = new ArrayCollection();
    }

    use HasIdTrait;
    use HasSessionStageTrait;
    use HasPreferencesTrait;
    use HasAffectationStageTrait;
    use HasEtatsTrait;

    use HasValidationStageTrait;
    use HasEtudiantTrait;
    use HasConventionStageTrait;
    use HasContactsStagesTrait;
    use HasConventionStageSignatairesTrait;
    use HasTagsTrait;
    /**
     * @var Stage|null $stagePrincipal
     * @var Stage|null $stageSecondaire
     * @var bool $isStageSecondaire
     */
    protected bool $isStageSecondaire = false;
    protected ?Stage $stagePrincipal = null;
    protected ?Stage $stageSecondaire = null;

    /**
     * @var int|null
     */
    protected ?int $ordreAffectation = 0;

    /**
     * @var float|null
     */
    protected ?float  $scorePreAffectation = 0;
    /**
     * @var bool
     */

    protected bool  $stageNonEffectue = false;
    /**
     * @var Stage|null
     */
    protected ?Stage $stagePrecedent;
    /**
     * @var Stage|null
     */
    protected ?Stage $stageSuivant;
    /**
     * Nombre floatant car en cas de stage_secondaire on les distingue par n.1 pour le principal et n.2 pour le secondaire
     * @var float|null
     */
    protected ?float $numero = 0;
    /**
     * @var int|null
     */
    protected ?int $ordreAffectationManuel = null;
    /**
     * @var float|null
     */
    protected ?float $scorePostAffectation = 0;
    /**
     * @var string|null
     */
    protected ?string $informationsComplementaires;
    /**
     * @var int|null
     */
    protected ?int $ordreAffectationAutomatique = null;


    public static function sortStage(array $stages, ?string $ordre = 'asc'): array
    {
        $ordre = ($ordre != 'desc') ? 1 : -1;
        usort($stages, function (Stage $s1, Stage $s2) use ($ordre) {
            if($s1->isStageSecondaire()
                && $s1->getStagePrincipal()->getId() == $s2->getId()
            ){
                return 1;
            }
            if($s2->isStageSecondaire()
                && $s2->getStagePrincipal()->getId() == $s1->getId()
            ){
                return -1;
            }
            $n1 = intval($s1->getNumero(true));
            $n2 = intval($s2->getNumero(true));
            return $ordre * ($n1 - $n2);
        });
        return $stages;
    }

    /**
     * Get numero.
     *
     * @return ?float
     */
    public function getNumero(bool $complete = false): float|null
    {
        if(!$complete){return intval($this->numero);}
        return $this->numero;
    }

    /**
     * Set numero.
     *
     * @param float $numero
     *
     * @return Stage
     */
    public function setNumero(float $numero): static
    {
        $this->numero = $numero;
        return $this;
    }


    public function getLibelle() : string
    {
        return $this->sessionStage->getLibelle();
//        return sprintf("n°%s", $this->getNumero());
    }

    /**
     * @return DateTime|null
     */
    public function getDateDebutChoix(): ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateDebutChoix();
    }

    /**
     * @return DateTime|null
     */
    public function getDateFinChoix(): ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateFinChoix();
    }

    /**
     * @return DateTime|null
     */
    public function getDateCommission() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateCommission();
    }

    /**
     * @return DateTime|null
     */
    public function getDateFinCommission() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateFinCommission();
    }

    /**
     * @return DateTime|null
     */
    public function getDateDebutStage() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateDebutStage();
    }

    /**
     * @return DateTime|null
     */
    public function getDateFinStage() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateFinStage();
    }

    /**
     * @return DateTime|null
     */
    public function getDateDebutValidation() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateDebutValidation();
    }

    /**
     * @return DateTime|null
     */
    public function getDateFinValidation() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateFinValidation();
    }

    /**
     * @return DateTime|null
     */
    public function getDateDebutEvaluation() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateDebutEvaluation();
    }

    /**
     * @return DateTime|null
     */
    public function getDateFinEvaluation() : ?DateTime
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getDateFinEvaluation();
    }

    /**
     * @return \Application\Entity\Db\AnneeUniversitaire|null
     */
    public function getAnneeUniversitaire() : ?AnneeUniversitaire
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getAnneeUniversitaire();
    }

    /**
     * @return NiveauEtude|null
     */
    public function getNiveauEtude() : ?NiveauEtude
    {
        if(!$this->hasSessionStage()){return null;}
        $groupe = $this->getSessionStage()->getGroupe();
        return $groupe?->getNiveauEtude();
    }

    /**
     * @return \Application\Entity\Db\Groupe|null
     */
    public function getGroupe() : ?Groupe
    {
        if(!$this->hasSessionStage()){return null;}
        return $this->getSessionStage()->getGroupe();
    }

    /**
     * Add preference.
     *
     * @param \Application\Entity\Db\Preference $preference
     *
     * @return Stage
     */
    public function addPreference(Preference $preference) : static
    {
        if (isset($this->stagePrincipal)) {
            $this->stagePrincipal->addPreference($preference);
        }
        if (isset($this->stageSecondaire)) {
            $this->stageSecondaire->addPreference($preference);
        }
        if (!$this->preferences->contains($preference)) {
            $this->preferences[] = $preference;
        }
        return $this;
    }

    /**
     * Remove preference.
     *
     * @param \Application\Entity\Db\Preference $preference
     * @return \Application\Entity\Db\Stage
     */
    public function removePreference(Preference $preference) : static
    {
        if (isset($this->stagePrincipal)) {
            return $this->stagePrincipal->removePreference($preference);
        }
        if (isset($this->stageSecondaire)) {
            return $this->stageSecondaire->removePreference($preference);
        }
        $this->preferences->removeElement($preference);
        return $this;
    }

    /**
     * Get preferences.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreferences() : Collection
    {
        if ($this->isStageSecondaire && isset($this->stagePrincipal)) {
            $this->preferences = $this->stagePrincipal->getPreferences();
        }
        return $this->preferences;
    }

    /**
     * Get affectation.
     *
     * @return \Application\Entity\Db\AffectationStage|null
     */
    public function getAffectationStage() : ?AffectationStage
    {
        if ($this->isStageSecondaire && isset($this->stagePrincipal)) {
            $this->affectationStage = $this->stagePrincipal->getAffectationStage();
        }
        return $this->affectationStage;
    }

    public function getTerrainStage() : ?TerrainStage
    {
        $affectation = $this->getAffectationStage();
        if(!isset($affectation)){return null;}
        return ($this->isStagePrincipal()) ? $affectation->getTerrainStage() : $affectation->getTerrainStageSecondaire();
    }

    public function getCategorieStage() : ?CategorieStage
    {
        $terrain = $this->getTerrainStage();
        return (isset($terrain)) ? $terrain->getCategorieStage() : null;
    }


    /**
     * Set affectation.
     *
     * @param \Application\Entity\Db\AffectationStage|null $affectationStage
     *
     * @return Stage
     */
    public function setAffectationStage(Db\AffectationStage $affectationStage = null) : static
    {
        if (isset($this->stagePrincipal)) {
            $this->stagePrincipal->setAffectationStage($affectationStage);
        }
        if (isset($this->stageSecondaire)) {
            $this->stageSecondaire->setAffectationStage($affectationStage);
        }
        $this->affectationStage = $affectationStage;
        return $this;
    }

    public function hasEtatFutur() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::FUTUR);
    }

    public function hasEtatPhasePreferences() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::PHASE_PREFERENCE);
    }

    public function hasEtatPhaseAffectation() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::PHASE_AFFECTATION);
    }

    public function hasEtatAVenir() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::A_VENIR);
    }

    public function hasEtatEnCours() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::EN_COURS);
    }

    public function hasValidationEffectuee() : bool
    {
        return (isset($this->validationStage) && $this->validationStage->validationEffectue());
    }
    public function hasEtatPhaseValidation() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::PHASE_VALIDATION);
    }

    public function hasEtatValidationEnRetard() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::VALIDATION_EN_RETARD);
    }

    public function hasEvaluationEffectuee() : bool
    {//TODO : faire comme pour la validation
        return true;
    }
    public function hasEtatPhaseEvaluation() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::PHASE_EVALUATION);
    }

    public function hasEtatEvaluationEnRetard() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::EVALUATION_EN_RETARD);
    }

    public function isTermine() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::TERMINE_VALIDE)
            || $this->isEtatActif(StageEtatTypeProvider::TERMINE_NON_VALIDE)
            ;
    }

    public function hasEtatValide() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::TERMINE_VALIDE);
    }

    public function hasEtatNonValide() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::TERMINE_NON_VALIDE);
    }



    /**
     * Get stageNonEffectue.
     *
     * @return bool
     */
    public function getStageNonEffectue(): bool
    {
        return $this->stageNonEffectue;
    }

    /**
     * Set stageNonEffectue.
     *
     * @param bool $stageNonEffectue
     *
     * @return Stage
     */
    public function setStageNonEffectue(bool $stageNonEffectue): static
    {
        $this->stageNonEffectue = $stageNonEffectue;
        if($this->hasStageSecondaire() && $this->stageSecondaire->isNonEffectue() != $stageNonEffectue){
            $this->stageSecondaire->setStageNonEffectue($stageNonEffectue);
        }
        if($this->hasStagePrincipal() && $this->stagePrincipal->isNonEffectue() != $stageNonEffectue){
            $this->stagePrincipal->setStageNonEffectue($stageNonEffectue);
        }
        return $this;
    }

    /**
     * Get stageNonEffectue.
     *
     * @return bool
     */
    public function isNonEffectue(): bool
    {
        return $this->stageNonEffectue;
    }

    public function hasEtatNonEffectue() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::NON_EFFECTUE);
    }

    public function hasEtatEnDisponibilite() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::EN_DISPO);
    }

    public function hasEtatEnAlerte() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::EN_AlERTE);
    }

    public function hasEtatEnErreur() : bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::EN_ERREUR);
    }

    public function hasEtatDesactive(): bool
    {
        return $this->isEtatActif(StageEtatTypeProvider::DESACTIVE);
    }

    /**
     * @return boolean
     */
    public function isVisibleByEtudiant(): bool
    {
        return $this->sessionStage->isVisibleByEtudiants() && !$this->hasEtatDesactive() && !$this->hasEtatEnErreur();
    }

    /**
     * Get ordreAffectation.
     *
     * @return int|null
     */
    public function getOrdreAffectation() : ?int
    {
        return $this->ordreAffectation;
    }
    /**
     * Set ordreAffectation.
     *
     * @param int|null $ordreAffectation
     *
     * @return Stage
     */
    public function setOrdreAffectation(int $ordreAffectation = null): static
    {
        $this->ordreAffectation = $ordreAffectation;
        if($this->hasStageSecondaire() && $this->stageSecondaire->getOrdreAffectation() != $ordreAffectation){
            $this->stageSecondaire->setOrdreAffectation($ordreAffectation);
        }
        if($this->hasStagePrincipal() && $this->stagePrincipal->getOrdreAffectation() != $ordreAffectation){
            $this->stagePrincipal->setOrdreAffectation($ordreAffectation);
        }

        return $this;
    }


    /**
     * Get scorePreAffectation.
     *
     * @return float|null
     */
    public function getScorePreAffectation() : ?float
    {
        return $this->scorePreAffectation;
    }

    /**
     * Set scorePreAffectation.
     *
     * @param float|null $scorePreAffectation
     *
     * @return Stage
     */
    public function setScorePreAffectation(?float $scorePreAffectation = null): static
    {
        $this->scorePreAffectation = $scorePreAffectation;
        if($this->hasStageSecondaire() && $this->stageSecondaire->getScorePreAffectation() != $scorePreAffectation){
            $this->stageSecondaire->setScorePreAffectation($scorePreAffectation);
        }
        if($this->hasStagePrincipal() && $this->stagePrincipal->getScorePreAffectation() != $scorePreAffectation){
            $this->stagePrincipal->setScorePreAffectation($scorePreAffectation);
        }

        return $this;
    }

    /**
     * Get stagePrecedent.
     *
     * @return \Application\Entity\Db\Stage|null
     */
    public function getStagePrecedent(): ?Stage
    {
        return $this->stagePrecedent;
    }

    /**
     * Set stagePrecedent.
     *
     * @param \Application\Entity\Db\Stage|null $stagePrecedent
     *
     * @return Stage
     */
    public function setStagePrecedent(Stage $stagePrecedent = null): static
    {
        $this->stagePrecedent = $stagePrecedent;
        return $this;
    }

    /**
     * Get stageSuivant.
     *
     * @return \Application\Entity\Db\Stage|null
     */
    public function getStageSuivant(): ?Stage
    {
        return $this->stageSuivant;
    }

    /**
     * Set stageSuivant.
     *
     * @param \Application\Entity\Db\Stage|null $stageSuivant
     *
     * @return Stage
     */
    public function setStageSuivant(Stage $stageSuivant = null): static
    {
        $this->stageSuivant = $stageSuivant;
        return $this;
    }

    /**
     * Get ordreAffectationManuel.
     *
     * @return int|null
     */
    public function getOrdreAffectationManuel(): ?int
    {
        return $this->ordreAffectationManuel;
    }

    /**
     * Set ordreAffectationManuel.
     *
     * @param int|null $ordreAffectationManuel
     *
     * @return Stage
     */
    public function setOrdreAffectationManuel(int $ordreAffectationManuel = null): static
    {
        $this->ordreAffectationManuel = $ordreAffectationManuel;
        if($this->hasStageSecondaire() && $this->stageSecondaire->getOrdreAffectationManuel() != $ordreAffectationManuel){
            $this->stageSecondaire->setOrdreAffectationManuel($ordreAffectationManuel);
        }
        if($this->hasStagePrincipal() && $this->stagePrincipal->getOrdreAffectationManuel() != $ordreAffectationManuel){
            $this->stagePrincipal->setOrdreAffectationManuel($ordreAffectationManuel);
        }

        return $this;
    }

    /**
     * Get scorePostAffectation.
     *
     * @return float|null
     */
    public function getScorePostAffectation(): ?float
    {
        return $this->scorePostAffectation;
    }

    /**
     * Set scorePostAffectation.
     *
     * @param float|null $scorePostAffectation
     *
     * @return Stage
     */
    public function setScorePostAffectation(float $scorePostAffectation = null): static
    {
        $this->scorePostAffectation = $scorePostAffectation;
        if($this->hasStageSecondaire() && $this->stageSecondaire->getScorePostAffectation() != $scorePostAffectation){
            $this->stageSecondaire->setScorePostAffectation($scorePostAffectation);
        }
        if($this->hasStagePrincipal() && $this->stagePrincipal->getScorePostAffectation() != $scorePostAffectation){
            $this->stagePrincipal->setScorePostAffectation($scorePostAffectation);
        }
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
     * Set informationsComplementaires.
     *
     * @param string|null $informationsComplementaires
     *
     * @return Stage
     */
    public function setInformationsComplementaires(string $informationsComplementaires = null): static
    {
        $this->informationsComplementaires = $informationsComplementaires;
        return $this;
    }

    /**
     * Get ordreAffectationAutomatique.
     *
     * @return int|null
     */
    public function getOrdreAffectationAutomatique(): ?int
    {
        return $this->ordreAffectationAutomatique;
    }

    /**
     * Set ordreAffectationAutomatique.
     *
     * @param int|null $ordreAffectationAutomatique
     *
     * @return Stage
     */
    public function setOrdreAffectationAutomatique(int $ordreAffectationAutomatique = null): static
    {
        $this->ordreAffectationAutomatique = $ordreAffectationAutomatique;
        if($this->hasStageSecondaire() && $this->stageSecondaire->getOrdreAffectationAutomatique() != $ordreAffectationAutomatique){
            $this->stageSecondaire->setOrdreAffectationAutomatique($ordreAffectationAutomatique);
        }
        if($this->hasStagePrincipal() && $this->stagePrincipal->getOrdreAffectationAutomatique() != $ordreAffectationAutomatique){
            $this->stagePrincipal->setOrdreAffectationAutomatique($ordreAffectationAutomatique);
        }
        return $this;
    }


    public function getStagePrincipal(): ?Stage
    {
        return $this->stagePrincipal;
    }

    public function setStagePrincipal(?Stage $stagePrincipal): static
    {
        $this->stagePrincipal = $stagePrincipal;
        return $this;
    }

    public function getStageSecondaire(): ?Stage
    {
        return $this->stageSecondaire;
    }

    public function setStageSecondaire(?Stage $stageSecondaire): static
    {
        $this->stageSecondaire = $stageSecondaire;
        return $this;
    }

    public function isStagePrincipal(): bool
    {
        return !$this->isStageSecondaire();
    }

    public function isStageSecondaire(): bool
    {
        return $this->isStageSecondaire;
    }

    public function setIsStageSecondaire(bool $isStageSecondaire): static
    {
        $this->isStageSecondaire = $isStageSecondaire;
        return $this;
    }

    public function setIsStagePrincipal(bool $isStagePrincipal): static
    {
        $this->setIsStageSecondaire(!$isStagePrincipal);
        return $this;
    }

    public function hasStagePrincipal(): bool
    {
        return isset($this->stagePrincipal);
    }

    public function hasStageSecondaire(): bool
    {
        return isset($this->stageSecondaire);
    }

}
