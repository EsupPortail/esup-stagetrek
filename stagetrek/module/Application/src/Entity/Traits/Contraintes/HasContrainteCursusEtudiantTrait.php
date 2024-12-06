<?php

namespace Application\Entity\Traits\Contraintes;

use Application\Entity\Db\ContrainteCursusEtudiant;

/**
 *
 */
trait HasContrainteCursusEtudiantTrait
{
    /**
     * @var \Application\Entity\Db\ContrainteCursusEtudiant|null
     */
    protected ?ContrainteCursusEtudiant $contrainteCursusEtudiant = null;


    /**
     * @return \Application\Entity\Db\ContrainteCursusEtudiant|null
     */
    public function getContrainteCursusEtudiant(): ?ContrainteCursusEtudiant
    {
        return $this->contrainteCursusEtudiant;
    }

    /**
     * @param \Application\Entity\Db\ContrainteCursusEtudiant|null $contrainteCursusEtudiant
     * @return \Application\Entity\Traits\HasContrainteCursusEtudiantTrait
     */
    public function setContrainteCursusEtudiant(?ContrainteCursusEtudiant $contrainteCursusEtudiant): static
    {
        $this->contrainteCursusEtudiant = $contrainteCursusEtudiant;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasContrainteCursusEtudiant(): bool
    {
        return $this->contrainteCursusEtudiant !== null;
    }
}