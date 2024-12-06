<?php

namespace Application\Entity\Traits\Contraintes;

use Application\Entity\Db\ContrainteCursusPortee;

/**
 *
 */
trait HasContrainteCursusPorteeTrait
{
    /**
     * @var \Application\Entity\Db\ContrainteCursusPortee|null
     */
    protected ?ContrainteCursusPortee $contrainteCursusPortee = null;

    /**
     * @return \Application\Entity\Db\ContrainteCursusPortee|null
     */
    public function getContrainteCursusPortee(): ?ContrainteCursusPortee
    {
        return $this->contrainteCursusPortee;
    }

    /**
     * @param \Application\Entity\Db\ContrainteCursusPortee|null $contrainteCursusPortee
     * @return \Application\Entity\Traits\HasContrainteCursusPorteeTrait
     */
    public function setContrainteCursusPortee(?ContrainteCursusPortee $contrainteCursusPortee): static
    {
        $this->contrainteCursusPortee = $contrainteCursusPortee;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContrainteCursusPortee(): bool
    {
        return $this->contrainteCursusPortee !== null;
    }
}