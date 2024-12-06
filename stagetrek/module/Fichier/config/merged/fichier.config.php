<?php

/**
 * Config concernant les fichiers NON liés à une thèse.
 */

namespace Fichier;
use Fichier\Controller\Factory\FichierControllerFactory;
use Fichier\Controller\FichierController;
use Fichier\Filter\FileName\DefaultFileNameFormatter;
use Fichier\Filter\FileName\NatureBasedFileNameFormatter;
use Fichier\Form\Upload\UploadForm;
use Fichier\Form\Upload\UploadFormFactory;
use Fichier\Form\Upload\UploadHydrator;
use Fichier\Provider\Privilege\FichierPrivileges;
use Fichier\Service\Fichier\FichierService;
use Fichier\Service\Fichier\FichierServiceFactory;
use Fichier\Service\Nature\NatureService;
use Fichier\Service\Nature\NatureServiceFactory;
use Fichier\View\Helper\FichierViewHelper;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;
use UnicaenUtilisateur\ORM\Event\Listeners\HistoriqueListener;

return [

    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => FichierController::class,
                    'action' => [
                        FichierController::ACTION_UPlOAD,
                    ],
                    'privileges' => [
                        FichierPrivileges::FICHIER_TELEVERSER,
                    ],
                ],
                [
                    'controller' => FichierController::class,
                    'action' => [
                        FichierController::ACTION_DOWNLOAD,
                    ],
                    'privileges' => [
                        FichierPrivileges::FICHIER_TELECHARGER,
                    ],
                ],
                [
                    'controller' => FichierController::class,
                    'action' => [
                        FichierController::ACTION_HISTORISER,
                    ],
                    'privileges' => [
                        FichierPrivileges::FICHIER_ARCHIVER,
                    ],
                ],
                [
                    'controller' => FichierController::class,
                    'action' => [
                        FichierController::ACTION_RESTAURER,
                    ],
                    'privileges' => [
                        FichierPrivileges::FICHIER_RESTAURER,
                    ],
                ],
                [
                    'controller' => FichierController::class,
                    'action' => [
                        FichierController::ACTION_DELETE,
                    ],
                    'privileges' => [
                        FichierPrivileges::FICHIER_SUPPRIMER,
                    ],
                ],
            ],
        ],
    ],
//    PRoblème dans les routes ? d
    'router'          => [
        'routes' => [
            'fichier' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/fichier',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'upload' => [
                        'type' => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/upload[/:nature]',
                            'constraints' => [
                                "affectationStage" => '[a-zA-Z0-9]+',
                            ],
                            'defaults' => [
                                'controller' => FichierController::class,
                                'action' => FichierController::ACTION_UPlOAD,
                            ],
                        ],
                    ],
                    'download' => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/download/:fichier',
                            'defaults' => [
                                'controller' => FichierController::class,
                                'action'     => FichierController::ACTION_DOWNLOAD
                            ],
                        ],
                    ],
                    'historiser' => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/historiser/:fichier',
                            'defaults' => [
                                'controller' => FichierController::class,
                                'action'     => FichierController::ACTION_HISTORISER,
                            ],
                        ],
                    ],
                    'restaurer' => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/restaurer/:fichier',
                            'defaults' => [
                                'controller' => FichierController::class,
                                'action'     => FichierController::ACTION_RESTAURER,
                            ],
                        ],
                    ],
                    'supprimer' => [
                        'type'          => Segment::class,
                        'may_terminate' => true,
                        'options' => [
                            'route'    => '/supprimer/:fichier',
                            'defaults' => [
                                'controller' => FichierController::class,
                                'action'     => FichierController::ACTION_DELETE,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'form_elements' => [
        'factories' => [
            UploadForm::class => UploadFormFactory::class,
        ],
    ],
    'hydrators' => [
        'invokables' => [
            UploadHydrator::class => UploadHydrator::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            DefaultFileNameFormatter::class => DefaultFileNameFormatter::class,
            NatureBasedFileNameFormatter::class => NatureBasedFileNameFormatter::class,
        ],
        'factories' => [
            FichierService::class => FichierServiceFactory::class,
            NatureService::class => NatureServiceFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            FichierController::class => FichierControllerFactory::class,
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'fichier' => FichierViewHelper::class,
        ],
    ],

    'doctrine' => [
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    HistoriqueListener::class,
                ],
            ],
        ],
    ],
];
