<?php

namespace Application\Entity\Traits\Parametre;

use Application\Entity\Db\Parametre;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;

/**
 *
 */
trait HasParametreTrait
{
    /**
     * @var Parametre|null
     */
    protected ?Parametre $parametre = null;

    /**
     * @return Parametre|null
     */
    public function getParametre(): ?Parametre
    {
        return $this->parametre;
    }

    /**
     * @param Parametre|null $parametre
     * @return HasParametreTrait
     */
    public function setParametre(?Parametre $parametre): static
    {
        $this->parametre = $parametre;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasParametre(): bool
    {
        return $this->parametre !== null;
    }

    /**
     * @var ParametreCoutAffectation|null
     */
    protected ?ParametreCoutAffectation $parametreCoutAffectation = null;

    /**
     * @return ParametreCoutAffectation|null
     */
    public function getParametreCoutAffectation(): ?ParametreCoutAffectation
    {
        return $this->parametreCoutAffectation;
    }

    /**
     * @param ParametreCoutAffectation|null $parametreCoutAffectation
     * @return HasParametreTrait
     */
    public function setParametreCoutAffectation(?ParametreCoutAffectation $parametreCoutAffectation): static
    {
        $this->parametreCoutAffectation = $parametreCoutAffectation;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasParametreCoutAffectation(): bool
    {
        return $this->parametreCoutAffectation !== null;
    }

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
     * @return HasParametreTrait
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