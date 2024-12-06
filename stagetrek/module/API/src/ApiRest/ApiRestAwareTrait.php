<?php

namespace API\ApiRest;

trait ApiRestAwareTrait
{
    /**
     * @var ApiRest|null
     */
    protected ?ApiRest $apiRest = null;


    /**
     * @param ApiRest $apiRest
     * @return ApiRestAwareTrait
     */
    public function setApiRest(ApiRest $apiRest) : static
    {
        $this->apiRest = $apiRest;
        return $this;
    }

    /**
     * @return ApiRest
     */
    public function getApiRest(): ApiRest
    {
        return $this->apiRest;
    }
}