<?php

namespace Application\Entity\Traits\Parametre;

use Application\Entity\Db\NiveauEtude;

/**
 *
 */
trait HasNiveauEtudeTrait
{

    /**
     * @var \Application\Entity\Db\NiveauEtude|null
     */
    protected ?NiveauEtude $niveauEtude = null;


    /**
     * @return NiveauEtude|null
     */
    public function getNiveauEtude(): ?NiveauEtude
    {
        return $this->niveauEtude;
    }

    /**
     * @param \Application\Entity\Db\NiveauEtude|null $niveauEtude
     * @return HasNiveauEtudeTrait
     */
    public function setNiveauEtude(?NiveauEtude $niveauEtude): static
    {
        $this->niveauEtude = $niveauEtude;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasNiveauEtude(): bool
    {
        return $this->niveauEtude !== null;
    }
}