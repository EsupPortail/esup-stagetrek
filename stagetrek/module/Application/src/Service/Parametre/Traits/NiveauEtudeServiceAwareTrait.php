<?php

namespace Application\Service\Parametre\Traits;

use Application\Service\Parametre\NiveauEtudeService;

/**
 * Traits NiveauEtudeServiceAwareTrait
 * @package Application\Service\AnneeUniversitaire
 */
Trait NiveauEtudeServiceAwareTrait
{
    /** @var ?NiveauEtudeService $niveauEtudeService */
    protected ?NiveauEtudeService $niveauEtudeService = null;

    /**
     * @return NiveauEtudeService
     */
    public function getNiveauEtudeService(): NiveauEtudeService
    {
        return $this->niveauEtudeService;
    }

    public function setNiveauEtudeService(NiveauEtudeService $niveauEtudeService): static
    {
        $this->niveauEtudeService = $niveauEtudeService;
        return $this;
    }
}