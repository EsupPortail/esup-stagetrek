<?php

namespace Application\Entity\Traits\Parametre;

use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;

/**
 *
 */
trait HasParametreCoutTerrainTrait
{
    /**
     * @var ParametreTerrainCoutAffectationFixe|null
     */
    protected ?ParametreTerrainCoutAffectationFixe $parametreTerrainCoutAffectationFixe = null;

    /**
     * @return ParametreTerrainCoutAffectationFixe|null
     */
    public function getParametreTerrainCoutAffectationFixe(): ?ParametreTerrainCoutAffectationFixe
    {
        return $this->parametreTerrainCoutAffectationFixe;
    }

    /**
     * @param ParametreTerrainCoutAffectationFixe|null $parametreTerrainCoutAffectationFixe
     * @return HasParametreCoutTerrainTrait
     */
    public function setParametreTerrainCoutAffectationFixe(?ParametreTerrainCoutAffectationFixe $parametreTerrainCoutAffectationFixe): static
    {
        $this->parametreTerrainCoutAffectationFixe = $parametreTerrainCoutAffectationFixe;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasParametreTerrainCoutAffectationFixe(): bool
    {
        return $this->parametreTerrainCoutAffectationFixe !== null;
    }
}