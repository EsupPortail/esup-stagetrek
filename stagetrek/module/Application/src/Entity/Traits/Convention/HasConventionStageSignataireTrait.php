<?php

namespace Application\Entity\Traits\Convention;

use Application\Entity\Db\ConventionStageSignataire;

/**
 * TODO : a revoir / spliter
 */
trait HasConventionStageSignataireTrait
{
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
     * @return HasConventionStageSignataireTrait
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