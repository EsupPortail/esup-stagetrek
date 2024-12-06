<?php

namespace API\Service\Traits;


use API\Service\VilleApiService;

trait VilleApiServiceAwareTrait
{
    /**
     * @var VilleApiService|null
     */
    protected ?VilleApiService $villeApiService = null;


    /**
     * @param VilleApiService $villeService
     * @return mixed
     */
    public function setVilleApiService(VilleApiService $villeService) : static
    {
        $this->villeApiService = $villeService;
        return $this;
    }

    /**
     * @return \API\Service\VilleApiService
     */
    public function getVilleApiService() : VilleApiService
    {
        return $this->villeApiService;
    }
}