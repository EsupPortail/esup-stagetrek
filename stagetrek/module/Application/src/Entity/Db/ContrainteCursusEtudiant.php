<?php
//TODo : continuer a splitter le code en plein de module afin de facilité la gestions du projet

namespace Application\Entity\Db;

use Application\Entity\Traits\Contraintes\HasContrainteCursusTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Provider\EtatType\ContrainteCursusEtudiantEtatTypeProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;

/**
 * ContrainteCursusEtudiant
 */
class ContrainteCursusEtudiant implements ResourceInterface, HasEtatsInterface
{
    const RESOURCE_ID = 'ContrainteCursusEtudiant';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    /** Fonction de trie d'un tableau de groupe */
    static function sortContraintes(array $contraintes): array
    {
        usort($contraintes, function (ContrainteCursusEtudiant $c1, ContrainteCursusEtudiant $c2) {
            //Trie par portée
            if ($c1->getPortee()->getId() != $c2->getPortee()->getId()) {
                return $c1->getPortee()->getOrdre() - $c2->getPortee()->getOrdre();
            }
            if($c1->hasPorteeCategorie()){
                $cat1 = $c1->getCategorieStage();
                $cat2 = $c2->getCategorieStage();
                if($cat1->isCategorieSecondaire() && !$cat2->isCategorieSecondaire()){
                    return 1;
                }
                if(!$cat1->isCategorieSecondaire() && $cat2->isCategorieSecondaire()){
                    return -1;
                }
            }
            if($c1->hasPorteeTerrain()){
                $t1 = $c1->getTerrainStage();
                $t2 = $c2->getTerrainStage();
                if($t1->isTerrainSecondaire() && !$t2->isTerrainSecondaire()){
                    return 1;
                }
                if($t1->isTerrainSecondaire() && !$t2->isTerrainSecondaire()){
                    return -1;
                }
            }
            if ($c1->getOrdre() != $c2->getOrdre()) {
                return $c1->getOrdre() - $c2->getOrdre();
            }
            return strcmp(strtolower($c1->getLibelle()), strtolower($c2->getLibelle()));
        });
        return $contraintes;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etats = new ArrayCollection();
    }

    use IdEntityTrait;
    use HasEtudiantTrait;
    use HasContrainteCursusTrait;
    use HasEtatsTrait;

    /**
     * @var int|null
     */
    private ?int $nbEquivalences = 0;
    /**
     * @var int|null
     */
    private ?int $nbStagesValidant = 0;
    /**
     * @var int|null
     */
    private ?int $resteASatisfaire = 0;

    /**
     * @var bool|null
     */
    private ?bool $active = true;
    /**
     * @var bool|null
     */
    private ?bool $valideeCommission = false;
    /**
     * @var bool|null
     */
    private ?bool $isSat = false;
    /**
     * @var bool|null
     */
    private ?bool $canBeSat = true;
    /**
     * @var bool|null
     */
    private ?bool $isInContradiction = false;

    /**
     * Get portee.
     *
     * @return \Application\Entity\Db\ContrainteCursusPortee|null
     */
    public function getPortee(): ?ContrainteCursusPortee
    {
        return $this->getContrainteCursus()->getContrainteCursusPortee();
    }

    public function getOrdre(): int
    {
        return $this->getContrainteCursus()->getOrdre();
    }

    /**
     * Get libelle.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->getContrainteCursus()->getLibelle();
    }

    public function getAcronyme(): ?string
    {
        return $this->getContrainteCursus()->getAcronyme();
    }
    public function getDescription(): string
    {
        return $this->getContrainteCursus()->getDescription();
    }


    public function hasPorteeGeneral(): bool
    {
        return $this->getContrainteCursus()->hasPorteeGeneral();
    }

    public function hasPorteeCategorie(): bool
    {
        return $this->getContrainteCursus()->hasPorteeCategorie();
    }

    public function hasPorteeTerrain(): bool
    {
        return $this->getContrainteCursus()->hasPorteeTerrain();
    }

    /**
     * Get categorieStage.
     *
     * @return \Application\Entity\Db\CategorieStage|null
     */
    public function getCategorieStage(): ?CategorieStage
    {
        return $this->getContrainteCursus()->getCategorieStage();
    }
    /**
     * Get terrainStage.
     *
     * @return \Application\Entity\Db\TerrainStage|null
     */
    public function getTerrainStage(): ?TerrainStage
    {
        return $this->getContrainteCursus()->getTerrainStage();
    }
    /**
     * Get min.
     *
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->getContrainteCursus()->getNombreDeStageMin();
    }

    /**
     * Get max.
     *
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->getContrainteCursus()->getNombreDeStageMax();
    }

    /**
     * Get nbEquivalences.
     *
     * @return int|null
     */
    public function getNbEquivalences(): ?int
    {
        return $this->nbEquivalences;
    }

    /**
     * Set nbEquivalences.
     *
     * @param int|null $nbEquivalences
     *
     * @return ContrainteCursusEtudiant
     */
    public function setNbEquivalences(int $nbEquivalences): static
    {
        $this->nbEquivalences = $nbEquivalences;
        return $this;
    }

    /**
     * Get nbStagesValidant.
     *
     * @return int|null
     */
    public function getNbStagesValidant(): ?int
    {
        return $this->nbStagesValidant;
    }

    /**
     * Set nbStagesValidant.
     *
     * @param int|null $nbStagesValidant
     *
     * @return ContrainteCursusEtudiant
     */
    public function setNbStagesValidant(int $nbStagesValidant): static
    {
        $this->nbStagesValidant = $nbStagesValidant;
        return $this;
    }

    /**
     * Get nbStagesValidant.
     *
     * @return int|null
     */
    public function getResteASatisfaire(): ?int
    {
        return $this->resteASatisfaire;
    }

    /**
     * Set nbStagesValidant.
     *
     * @param int|null $resteASatisfaire
     *
     * @return ContrainteCursusEtudiant
     */
    public function setResteASatisfaire(int $resteASatisfaire): static
    {
        $this->resteASatisfaire = $resteASatisfaire;
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
     * @return ContrainteCursusEtudiant
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active.
     *
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * Get valideeCommission.
     *
     * @return bool|null
     */
    public function getValideeCommission(): ?bool
    {
        return $this->valideeCommission;
    }

    /**
     * Set valideeCommission.
     *
     * @param bool|null $valideeCommission
     *
     * @return ContrainteCursusEtudiant
     */
    public function setValideeCommission(bool $valideeCommission): static
    {
        $this->valideeCommission = $valideeCommission;
        return $this;
    }

    /**
     * Get valideeCommission.
     *
     * @return bool|null
     */
    public function isValideeCommission(): ?bool
    {
        return $this->valideeCommission;
    }

    /**
     * Get isSat.
     *
     * @return bool|null
     */
    public function getIsSat(): ?bool
    {
        return $this->isSat;
    }

    /**
     * Set isSat.
     *
     * @param bool|null $isSat
     *
     * @return ContrainteCursusEtudiant
     */
    public function setIsSat(bool $isSat): static
    {
        $this->isSat = $isSat;
        return $this;
    }

    /**
     * Get isSat.
     *
     * @return bool|null
     */
    public function isSat(): ?bool
    {
        return $this->isSat;
    }

    /**
     * Get canBeSat.
     *
     * @return bool|null
     */
    public function getCanBeSat(): ?bool
    {
        return $this->canBeSat;
    }

    /**
     * Set canBeSat.
     *
     * @param bool|null $canBeSat
     *
     * @return ContrainteCursusEtudiant
     */
    public function setCanBeSat(bool $canBeSat): static
    {
        $this->canBeSat = $canBeSat;
        return $this;
    }

    /**
     * Get canBeSat.
     *
     * @return bool|null
     */
    public function canBeSat(): ?bool
    {
        return $this->canBeSat;
    }


    //////////////////////
    /// Etat
    //////////////////////
    /**
     * Get isInContradiction.
     *
     * @return bool|null
     */
    public function getIsInContradiction(): ?bool
    {
        return $this->isInContradiction;
    }

    /**
     * Set isInContradiction.
     *
     * @param bool|null $isInContradiction
     *
     * @return ContrainteCursusEtudiant
     */
    public function setIsInContradiction(bool $isInContradiction): static
    {
        $this->isInContradiction = $isInContradiction;
        return $this;
    }

    /**
     * Get isInContradiction.
     *
     * @return bool|null
     */
    public function isInContradiction(): ?bool
    {
        return $this->contrainteCursus->isContradictoire();
    }


    public function hasEtatNonSat(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::NON_SAT);
    }

    public function hasEtatSat(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::SAT);
    }


    public function hasEtatValideeCommission(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::VALIDE_COMMISSION);
    }

    public function hasEtatInactif(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::DESACTIVE);
    }

    public function hasEtatAlerte(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::WARNING);
    }

    public function hasEtatInsat(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::INSAT);
    }

    public function hasEtatErreur(): bool
    {
        return $this->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::EN_ERREUR);
    }

}
