<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\SessionStage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasSessionsStagesTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $sessionsStages;

    /**
     * @param SessionStage $sessionsStages
     * @return \Application\Entity\Traits\Stage\HasSessionsStagesTrait
     */
    public function addSessionStage(SessionStage $sessionsStages): static
    {
        if (!$this->sessionsStages->contains($sessionsStages)) {
            $this->sessionsStages->add($sessionsStages);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param SessionStage $sessionsStages
     * @return \Application\Entity\Traits\Stage\HasSessionsStagesTrait
     */
    public function removeSessionStage(SessionStage $sessionsStages): static
    {
        $this->sessionsStages->removeElement($sessionsStages);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessionsStages(): Collection
    {
        return $this->sessionsStages;
    }

    /**
     * @return void
     */
    protected function initSessionsStagesCollection(): void
    {
        $this->sessionsStages = new ArrayCollection();
    }
}