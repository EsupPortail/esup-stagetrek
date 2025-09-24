<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasAdresseInterface;
use Application\Entity\Interfaces\HasSourceInterface;
use Application\Entity\Traits\Adresse\HasAdresseTrait;
use Application\Entity\Traits\Contrainte\HasContraintesCursusEtudiantsTrait;
use Application\Entity\Traits\Etudiant\HasDisponibilitesTrait;
use Application\Entity\Traits\Groupe\HasGroupesTrait;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\Referentiel\HasSourceTrait;
use Application\Entity\Traits\Stage\HasSessionsStagesTrait;
use Application\Entity\Traits\Stage\HasStagesTrait;
use Application\Entity\Traits\Utilisateur\HasUserTrait;
use Application\Provider\EtatType\EtudiantEtatTypeProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenDbImport\Entity\Db\Interfaces\SourceAwareInterface;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareTrait;

/**
 * Etudiant
 */
class Etudiant implements ResourceInterface, HasAdresseInterface, HasEtatsInterface
, HasSourceInterface, HistoriqueAwareInterface
{
    const RESOURCE_ID = 'Etudiant';
    /**
     * @var string|null
     */
    protected ?string $numEtu = null;

    use HasIdTrait;
    use HasUserTrait;
    use HasAdresseTrait;
    use HasStagesTrait;
    use HasSessionsStagesTrait;
    use HasGroupesTrait;
    use HasDisponibilitesTrait;
    use HasContraintesCursusEtudiantsTrait;
    use HasEtatsTrait;

    //TODO : transformer le numéro étudiant en String
    /**
     * @var string|null
     */
    protected ?string $nom = null;
    /**
     * @var string|null
     */
    protected ?string $prenom = null;
    /**
     * @var string|null
     */
    protected ?string $email = null;
    /**
     * @var \DateTime|null
     */
    protected ?DateTime $dateNaissance = null;
    /**
     * Propriété spécifique pour les recommandations de préférences de l'étudiants
     * Ces propriété sont calculé par le services (et doivent l'être a chaque fois que l'on souhaite les affiché)
     */
    protected array $categoriesRecommandees = [];
    protected array $terrainsRecommandes = [];
    protected array $nbRecommandationTerrain = [];
    protected array $nbRecommandationCategorie = [];

    public function __construct()
    {
        //Création automatique de l'adresse
        $this->adresse = new Adresse();
        $this->initGroupesCollection();
        $this->initSessionsStagesCollection();
        $this->initStagesCollection();
        $this->initContraintesCursusEtudiants();
        $this->initDisponibilitesCollection();
        $this->etats = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function getUsername(): ?string
    {
        return ($this->user) ? $this->user->getUsername() : "";
    }

    /**
     * Get numEtu.
     *
     * @return string|null
     */
    public function getNumEtu(): ?string
    {
        return $this->numEtu;
    }

    /**
     * Set numEtu.
     *
     * @param string $numEtu
     *
     * @return Etudiant
     */
    public function setNumEtu(string $numEtu): static
    {
        $this->numEtu = $numEtu;
        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        if (isset($this->user)) {
            return $this->user->getDisplayName();
        } else {
            return sprintf("%s %s", $this->getNom(), $this->getPrenom());
        }
    }

    /**
     * Get nom.
     *
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return Etudiant
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string|null
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return Etudiant
     */
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Etudiant
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get dateNaissance.
     *
     * @return \DateTime|null
     */
    public function getDateNaissance(): ?DateTime
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance.
     *
     * @param \DateTime|null $dateNaissance
     *
     * @return Etudiant
     */
    public function setDateNaissance(?DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    /**
     * Get preference for a Session de stage
     *
     * @return Preference[]
     */
    public function getPreferenceForSession(SessionStage $s): array
    {
        $stage = $this->getStageFor($s);
        if (!$stage) {
            return [];
        }
        $pref = $stage->getPreferences()->toArray();
        return Preference::sortPreferences($pref);
    }

    /**
     * Get stages.
     * @param SessionStage $session
     * @param bool $stagePrincipal
     * @return \Application\Entity\Db\Stage|null
     */
    public function getStageFor(SessionStage $session, bool $stagePrincipal = true): ?Stage
    {
        /** @var Stage $stage */
        foreach ($this->stages as $stage) {
            if ($stage->getSessionStage()->getId() == $session->getId() && $stage->isStagePrincipal() == $stagePrincipal) {
                return $stage;
            }
        }
        return null;
    }


    public function hasEtatCursusEnConstruction(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::CURSUS_EN_CONSTRUCTION);
    }

    public function hasEtatCursusEnCours(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::CURSUS_EN_COURS);
    }

    public function hasEtatEnAlerte(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::EN_AlERTE);
    }

    public function hasEtatEnErreur(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::EN_ERREUR);
    }

    public function hasEtatEnDispo(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::EN_DISPO);
    }

    public function hasTermineCursus(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::CURSUS_VAlIDE)
            || $this->isEtatActif(EtudiantEtatTypeProvider::CURSUS_INVALIDE)
            ;
    }


    public function hasEtatCursusValide(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::CURSUS_VAlIDE);
    }

    public function hasEtatCursusNonValide(): bool
    {
        return $this->isEtatActif(EtudiantEtatTypeProvider::CURSUS_INVALIDE);
    }

    public function setRecommandationTerrain(TerrainStage $t, int $nb): static
    {
        $this->terrainsRecommandes[$t->getId()] = $t;
        $this->nbRecommandationTerrain[$t->getId()] = $nb;
        return $this;
    }

    public function setRecommandationCategorie(CategorieStage $c, int $nb): static
    {
        $this->categoriesRecommandees[$c->getId()] = $c;
        $this->nbRecommandationCategorie[$c->getId()] = $nb;
        return $this;
    }

    public function getCategoriesRecommandees(): array
    {
        $categories = $this->categoriesRecommandees;
        return CategorieStage::sort($categories);
    }

    public function getTerrainsRecommandeesFor(CategorieStage $c): array
    {
        $res = $this->getTerrainsRecommandees();
        return array_filter($res, function (TerrainStage $t) use ($c) {
            return $t->getCategorieStage()->getId() == $c->getId();
        });
    }

    public function getTerrainsRecommandees(): array
    {
        $terrains = $this->terrainsRecommandes;
        return TerrainStage::sort($terrains);
    }

    public function getNbRecommandationForTerrain(TerrainStage $t): int
    {
        return ($this->nbRecommandationTerrain[$t->getId()]) ?? 0;
    }

    public function getNbRecommandationForCategorie(CategorieStage $c): int
    {
        return ($this->nbRecommandationCategorie[$c->getId()]) ?? 0;
    }
    // Validation du cursus d'un étudiant
    // Choix fait pour le momement : validation manuelle, peut être a revoir
    /**
     * @var bool $cursusTermine
     */
    protected bool $cursusTermine = false;
    /**
     * @var bool $cursusValide
     */
    protected bool $cursusValide = false;

    public function hasCursusTermine(): bool
    {
        return $this->cursusTermine;
    }

    public function setCursusTermine(bool $cursusTermine): static
    {
        $this->cursusTermine = $cursusTermine;
        return $this;
    }

    public function hasCursusValide(): bool
    {
        return $this->cursusValide;
    }

    public function setCursusValide(bool $cursusValide): static
    {
        $this->cursusValide = $cursusValide;
        return $this;
    }


    public function getNiveauEtudeActuel() : ?NiveauEtude
    {
        $n = null;
        foreach ($this->getGroupes() as $g){
            /** @var NiveauEtude $ng */
            $ng = $g->getNiveauEtude();
            if(!isset($n) || $ng->getOrdre() < $n->getOrdre()){
                $n = $ng;
            }
        }
        return $n;
    }

    use HasSourceTrait;
    use HistoriqueAwareTrait;
    use HasCodeTrait;

    public function getCode(): ?string
    {
        return $this->numEtu;
    }
}
