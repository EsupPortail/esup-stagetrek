<?php

namespace API\ApiRest;

interface ApiRestAwareInterface
{
    /**
     * @param ApiRest $apiRest
     */
    public function setApiRest(ApiRest $apiRest);

    /**
     * @return ApiRest
     */
    public function getApiRest(): ApiRest;
}