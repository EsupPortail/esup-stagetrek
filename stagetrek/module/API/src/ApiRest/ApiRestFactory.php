<?php

namespace API\ApiRest;

use Interop\Container\ContainerInterface;
use Laminas\Http\Client as HttpClient;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ApiRestFactory implements FactoryInterface
{
    /**
     * Create ApiRest
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ApiRest
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ApiRest
    {
        $httpClient = new HttpClient();
        $httpClient->setAdapter(HttpClient\Adapter\Curl::class);

        return new ApiRest($httpClient);
    }
}