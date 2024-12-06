<?php

namespace API\Service\Traits;

use API\Service\ReferentielEtudiantApiService;

trait ReferentielEtudiantApiServiceAwareTrait
{
    /**
     * @var ReferentielEtudiantApiService|null
     */
    protected ?ReferentielEtudiantApiService $referentielEtudiantService = null;


    /**
     * @param ReferentielEtudiantApiService $villeService
     * @return mixed
     */
    public function setReferentielEtudiantApiService(ReferentielEtudiantApiService $referentielEtudiantService) : static
    {
        $this->referentielEtudiantService = $referentielEtudiantService;
        return $this;
    }

    /**
     * @return \API\Service\ReferentielEtudiantApiService
     */
    public function getReferentielEtudiantService() : ?ReferentielEtudiantApiService
    {
        return $this->referentielEtudiantService;
    }
}