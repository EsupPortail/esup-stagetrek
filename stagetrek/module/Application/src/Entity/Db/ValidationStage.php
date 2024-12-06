<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Provider\EtatType\ValidationStageEtatTypeProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenEtat\Entity\Db\HasEtatsTrait;

/**
 * ValidationStage
 */
class ValidationStage implements HasEtatsInterface
{
    use IdEntityTrait;
    use HasStageTrait;
    use HasEtatsTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etats = new ArrayCollection();
    }


    /**
     * @var bool $isValide
     * @var bool $isInvalide
     * @var bool $warning
     */
    protected bool $isValide = false;
    protected bool $isInvalide = false;
    protected ?bool $warning = false;



    public function isValide() : bool
    {
        return $this->isValide;
    }

    /**
     * Get isValide.
     *
     * @return bool
     */
    public function getIsValide() : bool
    {
        return $this->isValide;
    }

    /**
     * Set isValide.
     *
     * @param bool $isValide
     *
     * @return ValidationStage
     */
    public function setIsValide(bool $isValide) : static
    {
        $this->isValide = $isValide;
        if ($isValide) {
            $this->isInvalide = false;
        }
        return $this;
    }

    /**
     * Get isNonValide.
     *
     * @return bool
     */
    public function getIsInvalide() :bool
    {
        return $this->isInvalide;
    }

    public function isInvalide() :bool
    {
        return $this->isInvalide;
    }

    /**
     * Set isNonValide.
     *
     * @param bool $isInvalide
     *
     * @return ValidationStage
     */
    public function setIsInvalide(bool $isInvalide) : static
    {
        $this->isInvalide = $isInvalide;
        if ($isInvalide) {
            $this->isValide = false;
        }
        return $this;
    }

    public function validationEffectue() : bool
    {
        return $this->isValide || $this->isInvalide;
    }

    /**
     * Get validationWarning.
     *
     * @return bool
     */
    public function getWarning() : bool
    {
        return $this->warning;
    }

    /**
     * Set validationWarning.
     *
     * @param bool $warning
     * @return ValidationStage
     */
    public function setWarning(bool $warning) : static
    {
        $this->warning = $warning;
        return $this;
    }

    /**
     * @var string|null $commentaire
     * @var string|null $commentaireCache
     */
    protected ?string $commentaire = null;
    protected ?string $commentaireCache = null;
    /**
     * Get commentaire.
     * @return string|null
     */
    public function getCommentaire() : ?string
    {
        if ($this->commentaire == "") {
            $this->commentaire = null;
        }
        return $this->commentaire;
    }

    /**
     * Set commentaire.
     *
     * @param string|null $commentaire
     *
     * @return ValidationStage
     */
    public function setCommentaire(?string $commentaire = null) : static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    /**
     * Get commentaireCache.
     *
     * @return string|null
     */
    public function getCommentaireCache() : ?string
    {
        if ($this->commentaireCache == "") {
            $this->commentaireCache = null;
        }
        return $this->commentaireCache;
    }

    /**
     * Set commentaireCache.
     *
     * @param string|null $commentaireCache
     *
     * @return ValidationStage
     */
    public function setCommentaireCache(?string $commentaireCache = null)  : static
    {
        $this->commentaireCache = $commentaireCache;
        return $this;
    }

    /**
     * TODO : supprimer updateBy ?
     * @var DateTime|null $dateValidation
     * @var DateTime|null $dateValidationUpdate
     * @var string|null $validateBy
     */
    protected ?DateTime $dateValidation = null;
    protected ?string $validateBy = null;
    /**
     * Get dateValidation.
     * @return DateTime|null
     */
    public function getDateValidation() : ?DateTime
    {
        return $this->dateValidation;
    }

    /**
     * Set dateValidation.
     *
     * @param DateTime|null $dateValidation
     *
     * @return ValidationStage
     */
    public function setDateValidation(?DateTime $dateValidation = null) : static
    {
        $this->dateValidation = $dateValidation;
        return $this;
    }

    /**
     * Get validateBy.
     *
     * @return string|null
     */
    public function getValidateBy() : ?string
    {
        return $this->validateBy;
    }

    /**
     * Set validateBy.
     *
     * @param string|null $validateBy
     *
     * @return ValidationStage
     */
    public function setValidateBy(?string $validateBy = null) : static
    {
        $this->validateBy = $validateBy;
        return $this;
    }

    /** Accés direct aux différents état */
    public function hasEtatFutur() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::FUTUR);
    }
    public function hasEtatEnAttente() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::EN_ATTENTE);
    }
    public function hasEtatEnRetard() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::EN_RETARD);
    }

    public function hasEtatValide() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::VALIDE);
    }

    public function hasEtatInvalide() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::INVAlIDE);
    }

    public function hasEtatStageNonEffectue() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::STAGE_NON_EFFECTUE);
    }

    public function hasEtatEtudiantEnDispo() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::EN_DISPO);
    }

    public function hasEtatEnAlerte() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::EN_ALERTE);
    }

    public function hasEtatEnErreur() : bool
    {
        return $this->isEtatActif(ValidationStageEtatTypeProvider::EN_ERREUR);
    }
}
