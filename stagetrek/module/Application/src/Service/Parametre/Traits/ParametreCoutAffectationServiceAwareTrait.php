<?php

namespace Application\Service\Parametre\Traits;

use Application\Service\Parametre\ParametreCoutAffectationService;

/**
 * Traits ParametreServiceAwareTrait
 * @package Application\Service\Parametre
 */
Trait ParametreCoutAffectationServiceAwareTrait
{

    /** @var ?ParametreCoutAffectationService $parametreCoutAffectationService */
    protected ?ParametreCoutAffectationService $parametreCoutAffectationService = null;

    /**
     * @return ParametreCoutAffectationService
     */
    public function getParametreCoutAffectationService(): ParametreCoutAffectationService
    {
        return $this->parametreCoutAffectationService;
    }

    /**
     * @param ParametreCoutAffectationService $parametreCoutAffectationService
     * @return ParametreCoutAffectationServiceAwareTrait
     */
    public function setParametreCoutAffectationService(ParametreCoutAffectationService $parametreCoutAffectationService) : static
    {
        $this->parametreCoutAffectationService = $parametreCoutAffectationService;
        return $this;
    }
}