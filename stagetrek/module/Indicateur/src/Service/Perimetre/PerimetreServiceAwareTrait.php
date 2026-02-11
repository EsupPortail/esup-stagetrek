<?php

namespace Indicateur\Service\Perimetre;

trait PerimetreServiceAwareTrait
{
    private PerimetreService $perimetreService;

    public function getPerimetreService(): PerimetreService
    {
        return $this->perimetreService;
    }

    public function setPerimetreService(PerimetreService $perimetreService): void
    {
        $this->perimetreService = $perimetreService;
    }


}