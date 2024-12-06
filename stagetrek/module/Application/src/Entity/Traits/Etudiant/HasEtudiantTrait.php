<?php

namespace Application\Entity\Traits\Etudiant;

use Application\Entity\Db\Etudiant;

/**
 *
 */
trait HasEtudiantTrait
{
    /**
     * @var \Application\Entity\Db\Etudiant|null
     */
    protected ?Etudiant $etudiant = null;

    /**
     * @return \Application\Entity\Db\Etudiant|null
     */
    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    /**
     * @param \Application\Entity\Db\Etudiant|null $etudiant
     * @return void
     */
    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasEtudiant(): bool
    {
        return $this->etudiant !== null;
    }
}