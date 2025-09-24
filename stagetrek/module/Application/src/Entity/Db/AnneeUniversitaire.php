<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\LockableEntityInterface;
use Application\Entity\Traits\Groupe\HasGroupesTrait;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLockTrait;
use Application\Provider\EtatType\AnneeEtatTypeProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use PHPUnit\Framework\Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;
use UnicaenTag\Entity\Db\HasTagsInterface;
use UnicaenTag\Entity\Db\HasTagsTrait;

/**
 * AnneeUniversitaire
 */
class AnneeUniversitaire implements ResourceInterface, HasLibelleInterface, HasEtatsInterface
    , HasCodeInterface
    , HasTagsInterface
    , LockableEntityInterface
{
    const RESOURCE_ID = 'AnneeUniversitaire';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    /**
     * Constructor
     * @throws \DateMalformedStringException
     */
    public function __construct()
    {
        $this->initGroupesCollection();
        $this->etats = new ArrayCollection();
        //Initialisation de valeur par défaut des dates et du libellé
        //Choix arbitraire : on prévoit a priori l'année universitaire suivante
        // Qui a lieux du 01/09/X au 31/08/X+1
        $yearStar = date('Y');
        if (date('m') > 9) {
            $yearStar++;
        }
        $this->dateDebut = new DateTime('01-09-' . $yearStar);
        $this->dateDebut->setTime(0, 0);
        $this->dateFin = new DateTime('31-08-' . ($yearStar + 1));
        $this->dateFin->setTime(23, 59, 59);
        $this->libelle = sprintf("%s / %s", $yearStar, ($yearStar + 1));
    }

    use HasIdTrait;
    use HasCodeTrait;
    public function generateDefaultCode(array $param = []) : string
    {
        if(!isset($this->dateDebut)){
            throw new Exception("Impossible de généré le code de l'année : date de début non définie");
        }
        return $this->dateDebut->format('Y');
    }

    use HasLibelleTrait;
    use HasGroupesTrait;
    use HasEtatsTrait;

    public function getSessionsStages(): array
    {
        $sessions = [];
        /** @var Groupe $g */
        foreach ($this->getGroupes() as $g) {
            /** @var SessionStage $s */
            foreach ($g->getSessionsStages() as $s) {
                $sessions[$s->getId()] = $s;
            }
        }
        return $sessions;
    }

    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateDebut = null;
    /**
     * @var DateTime|null
     */
    protected ?DateTime $dateFin = null;

    /**
     * @var bool
     */
    protected bool $anneeVerrouillee = false;
    /**
     * @var \Application\Entity\Db\AnneeUniversitaire|null
     */
    protected ?AnneeUniversitaire $anneePrecedente = null;
    /**
     * @var \Application\Entity\Db\AnneeUniversitaire|null
     */
    protected ?AnneeUniversitaire $anneeSuivante = null;


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
     * @param DateTime $dateDebut
     * @return \Application\Entity\Db\AnneeUniversitaire
     */
    public function setDateDebut(DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        $this->code = $dateDebut->format('Y');
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
     * @param DateTime $dateFin
     * @return \Application\Entity\Db\AnneeUniversitaire
     */
    public function setDateFin(DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }


    /**
     * Get anneeVerrouillee.
     * @return bool
     * @deprecated Passage au système de lock par tag
     */
    public function getAnneeVerrouillee(): bool
    {
        return $this->anneeVerrouillee;
    }

    /**
     * Get anneeVerrouillee.
     * @return bool
     * @deprecated Passage au système de lock par tag
     */
    public function isAnneeVerrouillee(): bool
    {
        return $this->anneeVerrouillee;
    }

    use HasTagsTrait;
    use HasLockTrait;

    /**
     * Get anneeVerrouillee.
     * @deprecated Passage au système de lock par tag
     */
    public function isValidee(): bool
    {
        return $this->isAnneeVerrouillee();
    }

    /**
     * Get anneeVerrouillee.
     * @deprecated Passage au système de lock par tag
     */
    public function isNonValidee(): bool
    {
        return !$this->isAnneeVerrouillee();
    }

    /**
     * Set anneeVerrouillee.
     *
     * @param bool $anneeVerrouillee
     * @return \Application\Entity\Db\AnneeUniversitaire
     * @deprecated Passage au système de lock par tag
     */
    public function setAnneeVerrouillee(bool $anneeVerrouillee): static
    {
        $this->anneeVerrouillee = $anneeVerrouillee;
        return $this;
    }


    /**
     * Get anneePrecedente.
     *
     * @return AnneeUniversitaire|null
     */
    public function getAnneePrecedente(): ?AnneeUniversitaire
    {
        return $this->anneePrecedente;
    }

    /**
     * Set anneePrecedente.
     *
     * @param AnneeUniversitaire|null $anneePrecedente
     *
     */
    public function setAnneePrecedente(AnneeUniversitaire $anneePrecedente = null): static
    {
        $this->anneePrecedente = $anneePrecedente;
        return $this;
    }

    /**
     * Get anneeSuivante.
     *
     * @return \Application\Entity\Db\AnneeUniversitaire|null
     */
    public function getAnneeSuivante(): ?AnneeUniversitaire
    {
        return $this->anneeSuivante;
    }

    /**
     * Set anneeSuivante.
     *
     * @param \Application\Entity\Db\AnneeUniversitaire|null $anneeSuivante
     */
    public function setAnneeSuivante(AnneeUniversitaire $anneeSuivante = null): static
    {
        $this->anneeSuivante = $anneeSuivante;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasEtatEnCours(): bool
    {
        return $this->isEtatActif(AnneeEtatTypeProvider::EN_COURS);
    }

    public function hasEtatFuture(): bool
    {
        return $this->isEtatActif(AnneeEtatTypeProvider::FURTUR);
    }

    public function hasEtatTerminee(): bool
    {
        return $this->isEtatActif(AnneeEtatTypeProvider::TERMINE);
    }

    public function hasEtatNonValide(): bool
    {
        return $this->isEtatActif(AnneeEtatTypeProvider::NON_VAlIDE);
    }

    public function hasEtatEnConstruction(): bool
    {
        return $this->isEtatActif(AnneeEtatTypeProvider::EN_CONSTRUCTION);
    }
}
