<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\AffectationStage;

/**
 *
 */
Trait HasAffectationStageTrait
{
    /**
     * @var \Application\Entity\Db\AffectationStage|null
     */
    protected ?AffectationStage $affectationStage = null;

    /**
     * @return \Application\Entity\Db\AffectationStage|null
     */
    public function getAffectationStage(): ?AffectationStage
    {
        return $this->affectationStage;
    }

    /**
     * @param \Application\Entity\Db\AffectationStage|null $affectationStage
     * @return \Application\Entity\Traits\HasAffectationStageTrait
     */
    public function setAffectationStage(?AffectationStage $affectationStage): static
    {
        $this->affectationStage = $affectationStage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAffectationStage(): bool
    {
        return $this->affectationStage !== null;
    }
}