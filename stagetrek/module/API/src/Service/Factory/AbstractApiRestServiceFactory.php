<?php

namespace API\Service\Factory;

use API\ApiRest\ApiRest;
use API\Service\Abstract\AbstractApiRestService;
use API\Service\Interfaces\ApiRestServiceInterface;
use Application\Service\Misc\Entity\ModuleOptions;
use Interop\Container\ContainerInterface;
use Laminas\Http\Client as HttpClient;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use UnicaenApp\Exception\LogicException;

class AbstractApiRestServiceFactory implements AbstractFactoryInterface
{
    const API_PARAMS_KEY = 'api';

    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return class_exists($requestedName)
            && in_array(ApiRestServiceInterface::class, class_implements($requestedName));
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var ModuleOptions $moduleOptions
         */
        $moduleOptions = $container->get(ModuleOptions::class);
        // check config exists
        $params = $moduleOptions->getHttpclient();
        if (!array_key_exists(self::API_PARAMS_KEY, $params)) {
            throw new LogicException(sprintf("La clé d'accès aux paramètres de configuration des API '%s' est introuvable.", self::API_PARAMS_KEY));
        }

        /**
         * @var AbstractApiRestService $service
         */
        $apiParamsKey = $requestedName::getParamsApiKey();
        if (!$apiParamsKey) {
            throw new LogicException(sprintf("La clé d'accès aux paramètres de configuration de l'API '%s::getParamsApiKey()' n'est pas définie.", __CLASS__));
        }
        if (!array_key_exists($apiParamsKey, $params[self::API_PARAMS_KEY])) {
            throw new LogicException(sprintf("La clé d'accès aux paramètres de configuration de l'API '%s' est introuvable.", $apiParamsKey));
        }

        // New instance and set API parameters
        $apiParams = $params[self::API_PARAMS_KEY][$apiParamsKey];
        $service = new $requestedName($apiParams);

        if (!$service->getApiUrl()) {
            throw new LogicException(sprintf("L'url d'accès à l'API '%s' est introuvable dans la configuration.", $service::getParamsApiKey()));
        }

        // Set REST client
        $httpClient = new HttpClient();
        $httpClient->setAdapter(HttpClient\Adapter\Curl::class);
        $apiRest = new ApiRest($httpClient);
        if (array_key_exists('timeout', $apiParams)) {
            $apiRest->setOptions([
                    'timeout' => $params['timeout'],
                ]
            );
        }
        if (array_key_exists('use_proxy', $apiParams) && $apiParams['use_proxy'] === true) {
            $apiRest->setOptions([
                    'proxyhost' => $params['proxyhost'],
                    'proxyport' => $params['proxyport']
                ]
            );
        }
        $service->setApiRest($apiRest);

        return $service;
    }
}