<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\AffectationStage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasAffectationsStagesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $affectationsStages;

    /**
     * @param AffectationStage $affectationStage
     * @return \Application\Entity\Traits\Stage\HasAffectationsStagesTrait
     */
    public function addAffectationStage(AffectationStage $affectationStage): static
    {
        if (!$this->affectationsStages->contains($affectationStage)) {
            $this->affectationsStages->add($affectationStage);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param AffectationStage $affectationStage
     * @return \Application\Entity\Traits\Stage\HasAffectationsStagesTrait
     */
    public function removeAffectationStage(AffectationStage $affectationStage): static
    {
        $this->affectationsStages->removeElement($affectationStage);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAffectationsStages(): Collection
    {
        return $this->affectationsStages;
    }

    /**
     * @return void
     */
    protected function initAffectationsStagesCollection(): void
    {
        $this->affectationsStages = new ArrayCollection();
    }
}