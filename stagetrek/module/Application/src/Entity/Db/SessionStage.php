<?php

namespace Application\Entity\Db;

use Application\Entity\Db;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantsTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\Stage\HasAffectationsStagesTrait;
use Application\Entity\Traits\Stage\HasStagesTrait;
use Application\Entity\Traits\Terrain\HasTerrainsStagesTrait;
use Application\Provider\EtatType\SessionEtatTypeProvider;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;

/**
 * SessionStage
 */
class SessionStage implements ResourceInterface, HasLibelleInterface, HasEtatsInterface
{
    const RESOURCE_ID = 'SessionStage';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public static function sortSessions(array $sessions) : array
    {
        usort($sessions, function (SessionStage $s1, SessionStage $s2) {
            //Trie par année en premier
            $a1 = $s1->getAnneeUniversitaire();
            $a2 = $s2->getAnneeUniversitaire();
            //On trie par date de fin des années en ordre décroissant puis par libellé si c'est la même
            if ($a1->getId() != $a2->getId()) {
                if ($a1->getDateDebut() < $a2->getDateDebut()) return -1;
                if ($a2->getDateDebut() < $a1->getDateDebut()) return 1;
                if ($a1->getDateFin() < $a2->getDateFin()) return -1;
                if ($a2->getDateFin() < $a1->getDateFin()) return 1;
                return ($a1->getId() < $a2->getId()) ? -1 : 1;
            }
            $g1 = $s1->getGroupe();
            $g2 = $s2->getGroupe();
            //Trie par groupe / niveau d'étude
            $n1 = $g1->getNiveauEtude();
            $n2 = $g2->getNiveauEtude();
            if ($n1->getId() != $n2->getId()) {
                if ($n1->getOrdre() < $n2->getOrdre()) return -1;
                if ($n2->getOrdre() < $n1->getOrdre()) return 1;
                return strcmp($g1->getLibelle(), $g2->getLibelle());
            }
//            Même année, même groupe : trie par dates (de début en priorité)
            if ($s1->getDateDebutStage() < $s2->getDateDebutStage()) return -1;
            if ($s2->getDateDebutStage() < $s1->getDateDebutStage()) return 1;
            if ($s1->getDateFinStage() < $s2->getDateFinStage()) return -1;
            if ($s2->getDateFinStage() < $s1->getDateFinStage()) return 1;
            return strcmp($s1->getLibelle(), $s2->getLibelle());
        });
        return $sessions;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->terrainLinker = new ArrayCollection();
        $this->terrainsStages = new ArrayCollection();
        $this->initEtudiantsCollection();
        $this->initStagesCollection();
        $this->initAffectationsStagesCollection();
        $this->etats = new ArrayCollection();
    }

    use HasIdTrait;
    use HasLibelleTrait;
    use HasEtatsTrait;

    use HasAnneeUniversitaireTrait;
    use HasGroupeTrait;
    /**
     * Get niveauEtude.
     *
     * @return NiveauEtude|null
     */
    public function getNiveauEtude(): ?NiveauEtude
    {
        return ($this->hasGroupe()) ? $this->groupe->getNiveauEtude() : null;
    }

    use HasEtudiantsTrait;
    public function addEtudiant(Etudiant $etudiant): static
    {
        if(!$this->etudiants->contains($etudiant)){
            $this->etudiants->add($etudiant);
            $etudiant->addSessionStage($this);
        }
        return $this;
    }
    public function removeEtudiant(Db\Etudiant $etudiant):static
    {
        if(!$this->etudiants->contains($etudiant)){
            $this->etudiants->add($etudiant);
            $etudiant->removeSessionStage($this);
        }
        return $this;
    }


    use HasStagesTrait;
    /**
     * @return Stage[]
     */
    public function getStagesPrincipaux(): array
    {
        $stages = $this->getStages()->toArray();
        return array_filter($stages, function (Stage $s) {
            return $s->isStagePrincipal();
        });
    }
    /**
     * @return Stage[]
     */
    public function getStagesSecondaire(): array
    {
        $stages = $this->stages->toArray();
        return array_filter($stages, function (Stage $s) {
            return $s->isStageSecondaire();
        });
    }

    use HasAffectationsStagesTrait;
    public function getAffectations() : ArrayCollection
    {
        $affectations = new ArrayCollection();
        $etudiants = $this->etudiants->toArray();
        array_walk($etudiants, function (Etudiant $etudiant) use ($affectations) {
            $stage = $etudiant->getStageFor($this);
            if (!$stage) {
                return;
            } //Cas normalement impossible car signifierai qu'il n'y a pas de lien entre la session, le stage et l'étudiant
            if ($stage->getAffectationStage() != null) {
                $affectations[] = $stage->getAffectationStage();
            }
        });
        return $affectations;
    }

    /**
     * @var DateTime|null $dateCalculOrdresAffectations
     */
    protected ?DateTime $dateCalculOrdresAffectations = null;

    /**
     * @var DateTime|null $dateDebutChoix
     */
    protected ?DateTime  $dateDebutChoix = null;

    /**
     * @var DateTime|null $dateFinChoix
     */
    protected ?DateTime  $dateFinChoix = null;

    /**
     * @var DateTime|null $dateCommission
     */
    protected ?DateTime  $dateCommission = null;

    /**
     * @var DateTime|null $dateDebutStage
     */
    protected ?DateTime  $dateDebutStage = null;

    /**
     * @var DateTime|null $dateFinStage
     */
    protected ?DateTime  $dateFinStage = null;

    /**
     * @var DateTime|null $dateDebutValidation
     */
    protected ?DateTime  $dateDebutValidation = null;

    /**
     * @var DateTime|null $dateFinValidation
     */
    protected ?DateTime  $dateFinValidation = null;

    /**
     * @var DateTime|null $dateDebutEvaluation
     */
    protected ?DateTime  $dateDebutEvaluation = null;

    /**
     * @var DateTime|null $dateFinEvaluation
     */
    protected ?DateTime $dateFinEvaluation = null;

    /**
     * Get dateDebutStage.
     *
     * @return \DateTime|null
     */
    public function getDateDebutStage(): ?DateTime
    {
        return $this->dateDebutStage;
    }

    /**
     * Set dateDebutStage.
     *
     * @param \DateTime $dateDebutStage
     *
     * @return SessionStage
     */
    public function setDateDebutStage(DateTime $dateDebutStage): static
    {
        $this->dateDebutStage = $dateDebutStage;
        return $this;
    }

    /**
     * Get dateFinStage.
     *
     * @return \DateTime|null
     */
    public function getDateFinStage(): ?DateTime
    {
        return $this->dateFinStage;
    }

    /**
     * Set dateFinStage.
     *
     * @param \DateTime $dateFinStage
     *
     * @return SessionStage
     */
    public function setDateFinStage(DateTime $dateFinStage): static
    {
        $this->dateFinStage = $dateFinStage;
        return $this;
    }

    /**
     * Get dateCalculOrdresAffectations.
     *
     * @return \DateTime|null
     */
    public function getDateCalculOrdresAffectations(): ?DateTime
    {
        return $this->dateCalculOrdresAffectations;
    }

    /**
     * Set dateCalculOrdresAffectations.
     *
     * @param \DateTime $dateCalculOrdresAffectations
     *
     * @return SessionStage
     */
    public function setDateCalculOrdresAffectations(DateTime $dateCalculOrdresAffectations): static
    {
        $this->dateCalculOrdresAffectations = $dateCalculOrdresAffectations;
        return $this;
    }

    public function getDateFinCalculOrdresAffectations(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($this->dateCalculOrdresAffectations->getTimestamp());
        $date->add(DateInterval::createfromdatestring('+1 day'));
        return $date;
    }

    /**
     * Get dateDebutChoix.
     *
     * @return \DateTime|null
     */
    public function getDateDebutChoix(): ?DateTime
    {
        return $this->dateDebutChoix;
    }

    /**
     * Set dateDebutChoix.
     *
     * @param \DateTime $dateDebutChoix
     *
     * @return SessionStage
     */
    public function setDateDebutChoix(DateTime $dateDebutChoix): static
    {
        $this->dateDebutChoix = $dateDebutChoix;
        return $this;
    }

    /**
     * Get dateFinChoix.
     *
     * @return \DateTime|null
     */
    public function getDateFinChoix(): ?DateTime
    {
        return $this->dateFinChoix;
    }

    /**
     * Set dateFinChoix.
     *
     * @param \DateTime $dateFinChoix
     *
     * @return SessionStage
     */
    public function setDateFinChoix(DateTime $dateFinChoix): static
    {
        $this->dateFinChoix = $dateFinChoix;
        return $this;
    }

    /**
     * Get dateCommission.
     *
     * @return \DateTime|null
     */
    public function getDateCommission(): ?DateTime
    {
        return $this->dateCommission;
    }

    /**
     * Set dateCommission.
     *
     * @param \DateTime $dateCommission
     *
     * @return SessionStage
     */
    public function setDateCommission(DateTime $dateCommission): static
    {
        $this->dateCommission = $dateCommission;
        return $this;
    }

    /**
     * Retourne la commission d'affectation +1 jour, requis pour les test
     * @return \DateTime
     */
    public function getDateFinCommission(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($this->dateCommission->getTimestamp());
        $date->add(DateInterval::createfromdatestring('+1 day'));
        return $date;
    }

    /**
     * Get dateDebutValidation.
     *
     * @return \DateTime|null
     */
    public function getDateDebutValidation(): ?DateTime
    {
        return $this->dateDebutValidation;
    }

    /**
     * Set dateDebutValidation.
     *
     * @param \DateTime $dateDebutValidation
     *
     * @return SessionStage
     */
    public function setDateDebutValidation(DateTime $dateDebutValidation): static
    {
        $this->dateDebutValidation = $dateDebutValidation;
        return $this;
    }

    /**
     * Get dateFinValidation.
     *
     * @return \DateTime|null
     */
    public function getDateFinValidation(): ?DateTime
    {
        return $this->dateFinValidation;
    }

    /**
     * Set dateFinValidation.
     *
     * @param \DateTime $dateFinValidation
     *
     * @return SessionStage
     */
    public function setDateFinValidation(DateTime $dateFinValidation): static
    {
        $this->dateFinValidation = $dateFinValidation;
        return $this;
    }

    /**
     * Get dateDebutEvaluation.
     *
     * @return \DateTime|null
     */
    public function getDateDebutEvaluation(): ?DateTime
    {
        return $this->dateDebutEvaluation;
    }

    /**
     * Set dateDebutEvaluation.
     *
     * @param \DateTime $dateDebutEvaluation
     *
     * @return SessionStage
     */
    public function setDateDebutEvaluation(DateTime $dateDebutEvaluation): static
    {
        $this->dateDebutEvaluation = $dateDebutEvaluation;
        return $this;
    }

    /**
     * Get dateFinEvaluation.
     *
     * @return \DateTime|null
     */
    public function getDateFinEvaluation(): ?DateTime
    {
        return $this->dateFinEvaluation;
    }

    /**
     * Set dateFinEvaluation.
     *
     * @param \DateTime $dateFinEvaluation
     *
     * @return SessionStage
     */
    public function setDateFinEvaluation(DateTime $dateFinEvaluation): static
    {
        $this->dateFinEvaluation = $dateFinEvaluation;
        return $this;
    }

    /**
     * @var bool $sessionRattrapage
     */
    protected bool $sessionRattrapage = false;


    /**
     * @return bool
     */
    public function isSessionRattrapge() : bool
    {
        return $this->getSessionRattrapage();
    }

    /**
     * Get sessionRattrapage.
     *
     * @return bool
     */
    public function getSessionRattrapage() : bool
    {
        return $this->sessionRattrapage;
    }

    /**
     * Set sessionRattrapage.
     *
     * @param bool $sessionRattrapage
     *
     * @return SessionStage
     */
    public function setSessionRattrapage(bool $sessionRattrapage) : static
    {
        $this->sessionRattrapage = $sessionRattrapage;
        return $this;
    }

    /**
     * @var \Application\Entity\Db\SessionStage|null $sessionPrecedente
     */
    protected ?SessionStage $sessionPrecedente = null;
    /**
     * @var \Application\Entity\Db\SessionStage|null
     */
    protected ?SessionStage $sessionSuivante = null;
    /**
     * Get sessionPrecedente.
     *
     * @return \Application\Entity\Db\SessionStage|null
     */
    public function getSessionPrecedente() : ?SessionStage
    {
        return $this->sessionPrecedente;
    }

    /**
     * Set sessionPrecedente.
     *
     * @param \Application\Entity\Db\SessionStage|null $sessionPrecedente
     *
     * @return SessionStage
     */
    public function setSessionPrecedente(SessionStage $sessionPrecedente = null) : static
    {
        $this->sessionPrecedente = $sessionPrecedente;
        return $this;
    }

    /**
     * Get sessionSuivante.
     *
     * @return \Application\Entity\Db\SessionStage|null
     */
    public function getSessionSuivante(): ?SessionStage
    {
        return $this->sessionSuivante;
    }

    /**
     * Set sessionSuivante.
     *
     * @param \Application\Entity\Db\SessionStage|null $sessionSuivante
     *
     * @return SessionStage
     */
    public function setSessionSuivante(SessionStage $sessionSuivante = null) : static
    {
        $this->sessionSuivante = $sessionSuivante;
        return $this;
    }


    use HasTerrainsStagesTrait;

    /**
     * TODO : a revoir
     * Surcharges des accés aux terrains de stages pour le liens avec le terrainsStagesLinker
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTerrainsStages() : ArrayCollection
    {
        $this->terrainsStages = new ArrayCollection();
        /** @var SessionStageTerrainLinker $terrainLink */
        foreach ($this->getTerrainLinker() as $terrainLink) {
            $this->terrainsStages[] = $terrainLink->getTerrainStage();
        }
        return $this->terrainsStages;
    }

    /**
     * TODO : a revoir / renommé pas mis dans un trait car il n'y a que dans l'entité que l'on y accéde
     *
     * @var \Doctrine\Common\Collections\Collection|null
     */
    protected ?Collection $terrainLinker = null;

    /**
     * Add terrainLinker.
     *
     * @param \Application\Entity\Db\SessionStageTerrainLinker $terrainLinker
     *
     * @return SessionStage
     */
    public function addTerrainLinker(SessionStageTerrainLinker $terrainLinker) : static
    {
        if (!$this->terrainLinker->contains($terrainLinker)) {
            $this->terrainLinker->add($terrainLinker);
        }
        return $this;
    }

    /**
     * Get terrainLinker.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerrainLinker() : Collection
    {
        return $this->terrainLinker;
    }

    /**
     * Astuce permettant de retrouvé un terrain de stage par sont id
     * @param int $id
     * @return \Application\Entity\Db\TerrainStage|null
     */
    public function getTerrainStageWithId(int $id) : ?TerrainStage
    {
        /** @var TerrainStage $terrain */
        foreach ($this->getTerrainsStages() as $terrain) {
            if ($terrain->getId() == $id) {
                return $terrain;
            }
        }
        return null;
    }

    /**
     * Remove terrainLinker.
     *
     * @param \Application\Entity\Db\SessionStageTerrainLinker $terrainLinker
     * @return \Application\Entity\Db\SessionStage
     */
    public function removeTerrainLinker(Db\SessionStageTerrainLinker $terrainLinker): static
    {
        $this->getTerrainsStages()->removeElement($terrainLinker->getTerrainStage());
        $this->terrainLinker->removeElement($terrainLinker);
        return $this;
    }

    /**
     * Set nbPlacesOuvertes.
     * Modifie le nombre de places ouverte du SessionStageTerrainLinker associé au tuple Session/Terrain
     * Ne fais rien si la session n'est pas associé au terrain.
     * @param TerrainStage $terrain
     * @param int $nbPlacesOuvertes
     *
     * @return SessionStage
     */
    public function setNbPlacesOuvertes(TerrainStage $terrain, int $nbPlacesOuvertes) : static
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {//Le getTerrainsStage permet de s'assurer que la collection a été instancié
            return $this;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return $this;
        }
        $terrainLink->setNbPlacesOuvertes($nbPlacesOuvertes);
        return $this;
    }

    /**
     * Astuce permettant de récupérer le terrainStageLinker correspondant au terrain demandé
     * @param TerrainStage $terrain
     * @return \Application\Entity\Db\SessionStageTerrainLinker|null
     */
    public function getTerrainLinkFor(TerrainStage $terrain) : ?SessionStageTerrainLinker
    {
        $criteria = new Criteria();
        $expr = Criteria::expr();
        $criteria->andWhere($expr->eq('terrainStage', $terrain));
        /** @var \Doctrine\Common\Collections\Collection $res */
        $res = $this->terrainLinker->matching($criteria);
        return $res->first();
    }

    /**
     * @return int
     */
    public function getNbPlacesOuvertesTotal() : int
    {
        $cpt = 0;
        /** @var \Application\Entity\Db\TerrainStage $t */
        foreach ($this->getTerrainsStages() as $t) {
            if ($t->isTerrainPrincipal()) {
                $cpt += $this->getNbPlacesOuvertes($t);
            }
        }
        return $cpt;
    }

    /**
     * Get nbPlacesOuvertes.
     * Retourne le nombre de place ouverte pour le tuple session/terrain
     * retourne 0 si les 2 ne sont pas associé
     * @param TerrainStage $terrain
     * @return int
     */
    public function getNbPlacesOuvertes(TerrainStage $terrain) : int
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {
            return 0;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return 0;
        }
        return $terrainLink->getNbPlacesOuvertes();
    }

    /**
     * Set nbPlacesRecommandes.
     *
     * Modifie le nombre de places recommandé du SessionStageTerrainLinker associé au tuple Session/Terrain
     * Ne fais rien si la session n'est pas associé au terrain.
     * @param TerrainStage $terrain
     * @param int $nbPlacesRecommandees
     * @return SessionStage
     */
    public function setNbPlacesRecommandes(TerrainStage $terrain, int $nbPlacesRecommandees) : static
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {//Le getTerrainsStage permet de s'assurer que la collection a été instancié
            return $this;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return $this;
        }
        $terrainLink->setNbPlacesRecommandees($nbPlacesRecommandees);
        return $this;
    }

    /**
     * Get nbPlacesRecommandees.
     * Retourne le nombre de place recommandé pour le tuple session/terrain
     * retourne 0 si les 2 ne sont pas associés
     * @param TerrainStage $terrain
     * @return int
     * @return int|null
     */
    public function getNbPlacesPreAffectees(TerrainStage $terrain) : int
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {
            return 0;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return 0;
        }
        return $terrainLink->getNbPlacesPreAffectees();
    }

    /**
     * Get nbPlacesRecommandees.
     * Retourne le nombre de place recommandé pour le tuple session/terrain
     * retourne 0 si les 2 ne sont pas associés
     * @param TerrainStage $terrain
     * @return int
     * @return int|null
     */
    public function getNbPlacesAffectees(TerrainStage $terrain) : int
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {
            return 0;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return 0;
        }
        return $terrainLink->getNbPlacesAffectees();
    }

    /**
     * @return int
     */
    public function getNbPlacesRecommandeesTotales() : int
    {
        $cpt = 0;
        foreach ($this->getTerrainsStages() as $t) {
            $cpt += $this->getNbPlacesRecommandees($t);
        }
        return $cpt;
    }

    /**
     * Get nbPlacesRecommandees.
     * Retourne le nombre de place recommandé pour le tuple session/terrain
     * retourne 0 si les 2 ne sont pas associés
     * @param TerrainStage $terrain
     * @return int
     * @return int|null
     */
    public function getNbPlacesRecommandees(TerrainStage $terrain) : int
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {
            return 0;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return 0;
        }
        return $terrainLink->getNbPlacesRecommandees();
    }

    /**
     * Get nNbPlacesDisponibles.
     * Retourne le nombre de place recommandé pour le tuple session/terrain
     * retourne 0 si les 2 ne sont pas associés
     * @param TerrainStage $terrain
     * @return int
     * @return int|null
     */
    public function getNbPlacesDisponibles(TerrainStage $terrain) : int
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {
            return 0;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        if (!$terrainLink) {
            return 0;
        }
        return $terrainLink->getNbPlacesDisponibles();
    }


    public function getNiveauDemande(TerrainStage $terrain): ?TerrainStageNiveauDemande
    {
        if (!$this->getTerrainsStages()->contains($terrain)) {
            return null;
        }
        /** @var SessionStageTerrainLinker $terrainLink */
        $terrainLink = $this->getTerrainLinkFor($terrain);
        return $terrainLink?->getNiveauDemande();
    }



    /**
     * @return boolean
     */
    public function isVisibleByEtudiants() : bool
    {
        if($this->hasEtatDesactive()){return false;}
        if($this->hasEtatError()){return false;}
        return true;
    }

    public function hasEtatDesactive() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::DESACTIVE);
    }

    public function hasEtatError() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::EN_ERREUR);
    }

    public function hasEtatAlerte() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::EN_AlERTE);
    }

    public function hasEtatFutur() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::FUTUR);
    }

    public function hasEtatPhaseChoix() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::PHASE_PREFERENCE);
    }

    public function hasEtatPhaseAffectation() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::PHASE_AFFECTATION);
    }

    public function hasEtatAVenir() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::A_VENIR);
    }

    public function hasEtatEnCours() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::EN_COURS);
    }

    public function hasEtatPhaseValidation() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::PHASE_VALIDATION);
    }

    public function hasEtatPhaseEvaluation() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::PHASE_EVALUATION);
    }

    public function hasEtatTerminee() : bool
    {
        return $this->isEtatActif(SessionEtatTypeProvider::TERMINE);
    }
}
