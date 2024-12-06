<?php

namespace Application\Entity\Traits\Parametre;

use Application\Entity\Db\ParametreCoutAffectation;

/**
 *
 */
trait HasParametreCoutAffectationTrait
{
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
     * @return $this
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

}