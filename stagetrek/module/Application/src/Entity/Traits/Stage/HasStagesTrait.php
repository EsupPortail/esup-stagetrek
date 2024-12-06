<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\Stage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasStagesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $stages;

    /**
     * @param Stage $stages
     * @return \Application\Entity\Traits\HasStagesTrait
     */
    public function addStage(Stage $stages): static
    {
        if (!$this->stages->contains($stages)) {
            $this->stages->add($stages);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param Stage $stages
     * @return \Application\Entity\Traits\HasStagesTrait
     */
    public function removeStage(Stage $stages): static
    {
        $this->stages->removeElement($stages);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    /**
     * @return void
     */
    protected function initStagesCollection(): void
    {
        $this->stages = new ArrayCollection();
    }
}