<?php

namespace API;

use API\ApiRest\ApiRest;
use API\ApiRest\ApiRestFactory;
use API\Controller\LocalisationController;
use API\Controller\LocalisationControllerFactory;
use API\Controller\ReferentielEtudiantController;
use API\Controller\ReferentielEtudiantControllerFactory;
use API\Service\Factory\AbstractApiRestServiceFactory;
use API\Service\Factory\ReferentielEtudiantApiServiceFactory;
use API\Service\ReferentielEtudiantApiService;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => LocalisationController::class,
                    'action' => [
                        LocalisationController::RECHERCHER_VILLE_ACTION,
                    ],
                    'roles' => [],
                ],
                [
                    'controller' => ReferentielEtudiantController::class,
                    'action' => [
                        ReferentielEtudiantController::GET_ETUDIANTS_ACTION,
                    ],
                    'roles' => [],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api',
                    'defaults' => [
                        'controller' => '',
                        'action' => '',
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'ville' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ville',
                            'defaults' => [
                                'controller' => LocalisationController::class,
                                'action' => LocalisationController::RECHERCHER_VILLE_ACTION,
                            ],
                        ],
                    ],
                    'etudiants' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/etudiants',
                            'defaults' => [
                                'controller' => ReferentielEtudiantController::class,
                                'action' => ReferentielEtudiantController::GET_ETUDIANTS_ACTION,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            ApiRest::class => ApiRestFactory::class,
//            VilleApiService::class => VilleServiceFactory::class,
            ReferentielEtudiantApiService::class => ReferentielEtudiantApiServiceFactory::class,
        ],
        'abstract_factories' => [
            AbstractApiRestServiceFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            LocalisationController::class => LocalisationControllerFactory::class,
            ReferentielEtudiantController::class => ReferentielEtudiantControllerFactory::class,
        ],
    ],

    'hydrators' => [
        'factories' => [
        ],
    ],
];
