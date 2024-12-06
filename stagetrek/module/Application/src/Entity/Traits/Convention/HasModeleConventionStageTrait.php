<?php

namespace Application\Entity\Traits\Convention;

use Application\Entity\Db\ModeleConventionStage;

trait HasModeleConventionStageTrait
{ //TODO : renomé ModelConventionStage
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
     * @return \Application\Entity\Traits\HasModeleConventionStageTrait
     */
    public function setModeleConventionStage(?ModeleConventionStage $modeleConventionStage = null): static
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
}