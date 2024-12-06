<?php

namespace Application\Service\Parametre\Traits;

use Application\Service\Parametre\ParametreService;

/**
 * Traits ParametreServiceAwareTrait
 * @package Application\Service\Parametre
 */
Trait ParametreServiceAwareTrait
{
    /** @var ParametreService|null $parametres */
    protected ?ParametreService $parametreService = null;

    /**
     * @return \Application\Service\Parametre\ParametreService|null
     */
    public function getParametreService(): ?ParametreService
    {
        return $this->parametreService;
    }

    /**
     * @param ParametreService $parametreService
     * @return \Application\Service\Parametre\Traits\ParametreServiceAwareTrait
     */
    public function setParametreService(ParametreService $parametreService): static
    {
        $this->parametreService = $parametreService;
        return $this;
    }
}