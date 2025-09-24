<?php

namespace API;

use API\ApiRest\ApiRest;
use API\ApiRest\ApiRestFactory;
use API\Controller\LocalisationController;
use API\Controller\LocalisationControllerFactory;
use API\Service\Factory\AbstractApiRestServiceFactory;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            ApiRest::class => ApiRestFactory::class,
//            VilleApiService::class => VilleServiceFactory::class,
        ],
        'abstract_factories' => [
            AbstractApiRestServiceFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            LocalisationController::class => LocalisationControllerFactory::class,
        ],
    ],

    'hydrators' => [
        'factories' => [
        ],
    ],
];
