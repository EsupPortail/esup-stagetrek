<?php

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Fichier\Controller\Factory\IndexControllerFactory;
use Fichier\Controller\IndexController;
use Fichier\Provider\Privilege\FichierPrivileges;
use Laminas\Router\Http\Literal;
use UnicaenPrivilege\Guard\PrivilegeController;

return array(
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => IndexController::class,
                    'action' => [
                        IndexController::ACTION_INDEX,
                    ],
                    'privileges' => [
                        FichierPrivileges::FICHIER_AFFICHER,
                    ],
                ],
            ],
        ],
    ],
    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'Fichier\Entity\Db' => 'orm_default_xml_driver',
                ],
            ],
            'orm_default_xml_driver' => [
                'class' => XmlDriver::class,
                'cache' => 'Fichier',
                'paths' => [
                    __DIR__ . '/../src/Entity/Db/Mapping',
                ],
            ],
        ],
        'cache' => [
            'UnicaneFichier' => [
                'namespace' => 'FICHIER__' . __NAMESPACE__,
            ],
        ],
    ],

    'router'          => [
        'routes' => [
            'fichier' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/fichier',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => IndexController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
        ],
    ],
    'form_elements' => [
        'factories' => [
        ],
    ],
    'hydrators' => [
        'invokables' => [
        ],
    ],
    'view_helpers' => [
        'invokables' => [
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
);
