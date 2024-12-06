<?php

namespace Application\Entity\Traits\Contraintes;

use Application\Entity\Db\ContrainteCursus;

/**
 *
 */
trait HasContrainteCursusTrait
{
    /**
     * @var \Application\Entity\Db\ContrainteCursus|null
     */
    protected ?ContrainteCursus $contrainteCursus = null;

    /**
     * @return \Application\Entity\Db\ContrainteCursus|null
     */
    public function getContrainteCursus(): ?ContrainteCursus
    {
        return $this->contrainteCursus;
    }

    /**
     * @param \Application\Entity\Db\ContrainteCursus|null $contrainteCursus
     * @return \Application\Entity\Traits\HasContrainteCursusTrait
     */
    public function setContrainteCursus(?ContrainteCursus $contrainteCursus): static
    {
        $this->contrainteCursus = $contrainteCursus;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContrainteCursus(): bool
    {
        return $this->contrainteCursus !== null;
    }
}