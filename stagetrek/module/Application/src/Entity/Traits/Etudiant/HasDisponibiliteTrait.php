<?php

namespace Application\Entity\Traits\Etudiant;

use Application\Entity\Db\Disponibilite;

/**
 *
 */
trait HasDisponibiliteTrait
{

    /**
     * @var \Application\Entity\Db\Disponibilite|null
     */
    protected ?Disponibilite $disponibilite = null;

    /**
     * @return \Application\Entity\Db\Disponibilite|null
     */
    public function getDisponibilite(): ?Disponibilite
    {
        return $this->disponibilite;
    }

    /**
     * @param \Application\Entity\Db\Disponibilite|null $disponibilite
     * @return \Application\Entity\Traits\HasDisponibiliteTrait
     */
    public function setDisponibilite(?Disponibilite $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasDisponibilite(): bool
    {
        return $this->disponibilite !== null;
    }
}