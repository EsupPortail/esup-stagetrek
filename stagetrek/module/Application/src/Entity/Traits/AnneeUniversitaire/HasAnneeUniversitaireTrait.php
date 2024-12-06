<?php

namespace Application\Entity\Traits\AnneeUniversitaire;

use Application\Entity\Db\AnneeUniversitaire;

/**
 *
 */
Trait HasAnneeUniversitaireTrait
{
    /**
     * @var \Application\Entity\Db\AnneeUniversitaire|null
     */
    protected ?AnneeUniversitaire $anneeUniversitaire = null;

    /**
     * @return \Application\Entity\Db\AnneeUniversitaire|null
     */
    public function getAnneeUniversitaire(): ?AnneeUniversitaire
    {
        return $this->anneeUniversitaire;
    }


    /**
     * @param \Application\Entity\Db\AnneeUniversitaire|null $anneeUniversitaire
     * @return $this
     */
    public function setAnneeUniversitaire(?AnneeUniversitaire $anneeUniversitaire): static
    {
        $this->anneeUniversitaire = $anneeUniversitaire;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAnneeUniversitaire(): bool
    {
        return $this->anneeUniversitaire !== null;
    }
}