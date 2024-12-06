<?php
//Gestions des stages
use Application\Controller\Stage\Factory\StageControllerFactory;
use Application\Controller\Stage\Factory\ValidationStageControllerFactory;
use Application\Controller\Stage\StageController;
use Application\Controller\Stage\ValidationStageController;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Form\Stages\Factory\ValidationStageFieldsetFactory;
use Application\Form\Stages\Factory\ValidationStageFormFactory;
use Application\Form\Stages\Fieldset\ValidationStageFieldset;
use Application\Form\Stages\ValidationStageForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\StagePrivileges;
use Application\Service\Stage\Factory\StageServiceFactory;
use Application\Service\Stage\Factory\ValidationStageServiceFactory;
use Application\Service\Stage\StageService;
use Application\Service\Stage\ValidationStageService;
use Application\Validator\Actions\Factory\StageValidatorFactory;
use Application\Validator\Actions\Factory\ValidationStageValidatorFactory;
use Application\Validator\Actions\StageValidator;
use Application\Validator\Actions\ValidationStageValidator;
use Application\View\Helper\Stages\Factory\StageViewHelperFactory;
use Application\View\Helper\Stages\Factory\ValidationStageViewHelperFactory;
use Application\View\Helper\Stages\StageViewHelper;
use Application\View\Helper\Stages\ValidationStageViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => StageController::class,
                    'action' => [
                        StageController::ACTION_AFFICHER,
                        StageController::ACTION_AFFICHER_INFOS,
                        StageController::ACTION_AFFICHER_AFFECTATION,
                        StageController::ACTION_AFFICHER_CONVENTION,
                        StageController::ACTION_LISTER,
                        StageController::ACTION_LISTER_CONTACTS,
                    ],
                    'privileges' => [
                        StagePrivileges::STAGE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\Stage',
                ],
                [
                    'controller' => StageController::class,
                    'action' => [
                        StageController::ACTION_AJOUTER_STAGES,
                    ],
                    'privileges' => [
                        StagePrivileges::STAGE_AJOUTER
                    ],
                    'assertion' => 'Assertion\\Stage',
                ],
                [
                    'controller' => StageController::class,
                    'action' => [
                        StageController::ACTION_SUPPRIMER_STAGES,
                    ],
                    'privileges' => [
                        StagePrivileges::STAGE_SUPPRIMER
                    ],
                    'assertion' => 'Assertion\\Stage',
                ],
                [
                    'controller' => StageController::class,
                    'action' => [
                        StageController::ACTION_MON_STAGE,
                        StageController::ACTION_MES_STAGES,
                    ],
                    'privileges' => [
                        StagePrivileges::ETUDIANT_OWN_STAGES_AFFICHER
                    ],
                    'assertion' => 'Assertion\\Stage',
                ],
                [
                    'controller' => ValidationStageController::class,
                    'action' => [
                        ValidationStageController::ACTION_AFFICHER,
                    ],
                    'privileges' => [
                        StagePrivileges::VALIDATION_STAGE_AFFICHER
                    ],
                    'assertion' => 'Assertion\\ValidationStage',
                ],
                [
                    'controller' => ValidationStageController::class,
                    'action' => [
                        ValidationStageController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        StagePrivileges::VALIDATION_STAGE_MODIFIER
                    ],
                    'assertion' => 'Assertion\\ValidationStage',
                ],
                [
                    'controller' => ValidationStageController::class,
                    'action' => [
                        ValidationStageController::ACTION_VALIDER,
                    ],
                    //Cas spécifique car nécessite de vérifier la validité du token de validation ...
                    //Et de pas mal d'autres informations
                    'roles' => [],
                    'assertion' => 'Assertion\\ValidationStage',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                SessionStage::RESOURCE_ID => [],
                Stage::RESOURCE_ID => [],
                ArrayRessource::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            StagePrivileges::STAGE_AFFICHER,
                            StagePrivileges::STAGE_AJOUTER,
                            StagePrivileges::STAGE_MODIFIER,
                            StagePrivileges::STAGE_SUPPRIMER,
                            StagePrivileges::ETUDIANT_OWN_STAGES_AFFICHER,
                        ],
                        'resources' => [SessionStage::RESOURCE_ID, Stage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Stage',
                    ],
                    [
                        'privileges' => [
                            StagePrivileges::VALIDATION_STAGE_AFFICHER,
                            StagePrivileges::VALIDATION_STAGE_MODIFIER,
                        ],
                        'resources' => [Stage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ValidationStage',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'stage' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/stage',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:stage]',
                            'constraints' => [
                                "stage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_LISTER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_AJOUTER_STAGES,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_SUPPRIMER_STAGES,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'validation' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/validation',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'afficher' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/afficher[/:stage]',
                                    'constraints' => [
                                        'stage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ValidationStageController::class,
                                        'action' => ValidationStageController::ACTION_AFFICHER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'valider' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/valider[/:stage[/:token]]',
                                    'constraints' => [
                                        "stage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ValidationStageController::class,
                                        'action' => ValidationStageController::ACTION_VALIDER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:stage]',
                                    'constraints' => [
                                        "stage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ValidationStageController::class,
                                        'action' => ValidationStageController::ACTION_MODIFIER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'afficher-infos' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher-infos[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_AFFICHER_INFOS
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher-affectation' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher-affectation[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_AFFICHER_AFFECTATION
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher-convention' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher-convention[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ROUTE_AFFICHER_CONVENTION
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'lister-contacts' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister-contacts[/:stage]',
                            'defaults' => [
                                'controller' => StageController::class,
                                'action' => StageController::ACTION_LISTER_CONTACTS
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'mes-stages' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mes-stages',
                    'defaults' => [
                        'controller' => StageController::class,
                        'action' => StageController::ACTION_MES_STAGES,
                    ],
                ],
                'may_terminate' => true,
            ],
            'mon-stage' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mon-stage[/:stage]',
                    'constraints' => [
                        "stage" => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => StageController::class,
                        'action' => StageController::ACTION_MON_STAGE,
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            StageController::class => StageControllerFactory::class,
            ValidationStageController::class => ValidationStageControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            ValidationStageForm::class => ValidationStageFormFactory::class,
            ValidationStageFieldset::class => ValidationStageFieldsetFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            StageService::class => StageServiceFactory::class,
            ValidationStageService::class => ValidationStageServiceFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
        ],
    ],
    'validators' => [
        'factories' => [
            // Validateur spécifique pour la validation des stages par un token
            StageValidator::class => StageValidatorFactory::class,
            ValidationStageValidator::class => ValidationStageValidatorFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            "stage" => StageViewHelper::class,
            "validation" => ValidationStageViewHelper::class,
        ],
        'invokables' => [
        ],
        'factories' => [
            StageViewHelper::class => StageViewHelperFactory::class,
            ValidationStageViewHelper::class => ValidationStageViewHelperFactory::class,
        ]
    ],
    'session_containers' => [
    ],

];
