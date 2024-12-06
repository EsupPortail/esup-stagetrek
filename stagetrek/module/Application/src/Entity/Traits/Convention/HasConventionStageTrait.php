<?php

namespace Application\Entity\Traits\Convention;

use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\ConventionStageSignataire;
use Application\Entity\Db\ModeleConventionStage;

/**
 * TODO : a revoir / spliter
 */
trait HasConventionStageTrait
{

    /**
     * @var \Application\Entity\Db\ConventionStage|null
     */
    protected ?ConventionStage $conventionStage=null;

    /**
     * @return \Application\Entity\Db\ConventionStage|null
     */
    public function getConventionStage(): ?ConventionStage
    {
        return $this->conventionStage;
    }

    /**
     * @param \Application\Entity\Db\ConventionStage|null $conventionStage
     * @return HasConventionStageTrait
     */
    public function setConventionStage(?ConventionStage $conventionStage): static
    {
        $this->conventionStage = $conventionStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasConventionStage(): bool
    {
        return $this->conventionStage !== null;
    }

    /**
     * @var \Application\Entity\Db\ModeleConventionStage|null
     */
    protected ?ModeleConventionStage $modeleConventionStage = null;

    /**
     * @return \Application\Entity\Db\ModeleConventionStage|null
     */
    public function getModeleConventionStage(): ?ModeleConventionStage
    {
        return $this->modeleConventionStage;
    }

    /**
     * @param \Application\Entity\Db\ModeleConventionStage|null $modeleConventionStage
     * @return \Application\Entity\Traits\HasConventionStageTrait
     */
    public function setModeleConventionStage(?ModeleConventionStage $modeleConventionStage): static
    {
        $this->modeleConventionStage = $modeleConventionStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasModeleConventionStage(): bool
    {
        return $this->modeleConventionStage !== null;
    }

    /**
     * @var \Application\Entity\Db\ConventionStageSignataire|null
     */
    protected ?ConventionStageSignataire $conventionStageSignataire = null;

    /**
     * @return \Application\Entity\Db\ConventionStageSignataire|null
     */
    public function getConventionStageSignataire(): ?ConventionStageSignataire
    {
        return $this->conventionStageSignataire;
    }

    /**
     * @param \Application\Entity\Db\ConventionStageSignataire|null $conventionStageSignataire
     * @return HasConventionStageTrait
     */
    public function setConventionStageSignataire(?ConventionStageSignataire $conventionStageSignataire): static
    {
        $this->conventionStageSignataire = $conventionStageSignataire;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasConventionStageSignataire(): bool
    {
        return $this->conventionStageSignataire !== null;
    }

}