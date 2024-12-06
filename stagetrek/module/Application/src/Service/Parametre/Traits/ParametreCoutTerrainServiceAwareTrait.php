<?php

namespace Application\Service\Parametre\Traits;

use Application\Service\Parametre\ParametreCoutTerrainService;

/**
 * Traits ParametreServiceAwareTrait
 * @package Application\Service\Parametre
 */
Trait ParametreCoutTerrainServiceAwareTrait
{

    /** @var ParametreCoutTerrainService|null $parametreCoutTerrainService */
    protected ?ParametreCoutTerrainService $parametreCoutTerrainService = null;

    /**
     * @return ParametreCoutTerrainService
     */
    public function getParametreCoutTerrainService(): ParametreCoutTerrainService
    {
        return $this->parametreCoutTerrainService;
    }

    /**
     * @param ParametreCoutTerrainService $parametreCoutTerrainService
     * @return ParametreCoutTerrainServiceAwareTrait
     */
    public function setParametreCoutTerrainService(ParametreCoutTerrainService $parametreCoutTerrainService): static
    {
        $this->parametreCoutTerrainService = $parametreCoutTerrainService;
        return $this;
    }


}