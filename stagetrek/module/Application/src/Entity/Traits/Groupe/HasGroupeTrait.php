<?php

namespace Application\Entity\Traits\Groupe;

use Application\Entity\Db\Groupe;

/**
 *
 */
trait HasGroupeTrait
{
    /**
     * @var \Application\Entity\Db\Groupe|null
     */
    protected ?Groupe $groupe = null;

    /**
     * @return \Application\Entity\Db\Groupe|null
     */
    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    /**
     * @param \Application\Entity\Db\Groupe|null $groupe
     * @return \Application\Entity\Traits\HasGroupeTrait
     */
    public function setGroupe(?Groupe $groupe): static
    {
        $this->groupe = $groupe;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasGroupe(): bool
    {
        return $this->groupe !== null;
    }
}