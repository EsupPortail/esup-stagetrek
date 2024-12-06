<?php

namespace Fichier\Service\Fichier;

trait FichierServiceAwareTrait {

    /** @var FichierService $fichierService */
    private FichierService $fichierService;

    /**
     * @return FichierService
     */
    public function getFichierService(): FichierService
    {
        return $this->fichierService;
    }

    /**
     * @param FichierService $fichierService
     *
     * @return FichierService
     */
    public function setFichierService(FichierService $fichierService): FichierService
    {
        $this->fichierService = $fichierService;
        return $this->fichierService;
    }


}