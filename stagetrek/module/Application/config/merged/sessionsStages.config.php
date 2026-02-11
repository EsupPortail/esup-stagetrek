<?php
//Gestions des stages
use Application\Controller\Stage\Factory\SessionStageControllerFactory;
use Application\Controller\Stage\SessionStageController;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\SessionStage;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Form\Stages\Element\SessionStageEtatSelectPicker;
use Application\Form\Stages\Element\SessionStageSelectPicker;
use Application\Form\Stages\Factory\PeriodeStageFieldsetFactory;
use Application\Form\Stages\Factory\PeriodeStageFormFactory;
use Application\Form\Stages\Factory\PeriodeStageHydratorFactory;
use Application\Form\Stages\Factory\SessionStageFieldsetFactory;
use Application\Form\Stages\Factory\SessionStageFormFactory;
use Application\Form\Stages\Factory\SessionStageHydratorFactory;
use Application\Form\Stages\Factory\SessionStageRechercheFormFactory;
use Application\Form\Stages\Factory\SessionStageValidatorFactory;
use Application\Form\Stages\Fieldset\PeriodeStageFieldset;
use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Form\Stages\Hydrator\PeriodeStageHydrator;
use Application\Form\Stages\Hydrator\SessionStageHydrator;
use Application\Form\Stages\PeriodeStageForm;
use Application\Form\Stages\SessionStageForm;
use Application\Form\Stages\SessionStageRechercheForm;
use Application\Form\Stages\Validator\SessionStageValidator;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ContactPrivileges;
use Application\Provider\Privilege\SessionPrivileges;
use Application\Provider\Privilege\StagePrivileges;
use Application\Service\Stage\Factory\SessionStageServiceFactory;
use Application\Service\Stage\SessionStageService;
use Application\View\Helper\SessionsStages\Factory\SessionStageViewHelperFactory;
use Application\View\Helper\SessionsStages\SessionStageViewHelper;
use Application\View\Helper\Stages\CalendrierStageViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => SessionStageController::class,
                    'action' => [
                        SessionStageController::ACTION_INDEX,
                        SessionStageController::ACTION_AFFICHER,
                    ],
                    'privileges' => [
                        SessionPrivileges::SESSION_STAGE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\SessionStage',
                ],
                [
                    'controller' => SessionStageController::class,
                    'action' => [
                        SessionStageController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        SessionPrivileges::SESSION_STAGE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\SessionStage',
                ],
                [
                    'controller' => SessionStageController::class,
                    'action' => [
                        SessionStageController::ACTION_MODIFIER,
                        SessionStageController::ACTION_AJOUTER_PERIODE_STAGE,
                        SessionStageController::ACTION_MODIFIER_PERIODE_STAGE,
                        SessionStageController::ACTION_SUPPRIMER_PERIODE_STAGE,
                        SessionStageController::ACTION_MODIFIER_PLACES_TERRAINS,
                        SessionStageController::ACTION_IMPORTER_PLACES_TERRAINS,
                    ],
                    'privileges' => [
                        SessionPrivileges::SESSION_STAGE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\SessionStage',
                ],
                [
                    'controller' => SessionStageController::class,
                    'action' => [
                        SessionStageController::ACTION_MODIFIER_ORDRES_AFFECTATIONS,
                        SessionStageController::ACTION_RECALCULER_ORDRES_AFFECTATIONS,
                    ],
                    'privileges' => [
                        StagePrivileges::STAGE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\SessionStage',
                ],
                [
                    'controller' => SessionStageController::class,
                    'action' => [
                        SessionStageController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        SessionPrivileges::SESSION_STAGE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\SessionStage',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                SessionStage::RESOURCE_ID => [],
                AnneeUniversitaire::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            SessionPrivileges::SESSION_STAGE_AFFICHER,
                            SessionPrivileges::SESSION_STAGE_MODIFIER,
                            SessionPrivileges::SESSION_STAGE_SUPPRIMER,
                        ],
                        'resources' => [SessionStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\SessionStage',
                    ],
                    [ //Séparation spécifique car le privilége est commun avec StageAssertion. on ne prend ici que our une session de stage comme ressource (par de arrayCollection)
                        'privileges' => [
                            ContactPrivileges::SEND_MAIL_VALIDATION,
                        ],
                        'resources' => [SessionStage::RESOURCE_ID],
                        'assertion' => 'Assertion\\SessionStage',
                    ],
                    [
                        'privileges' => [
                            SessionPrivileges::SESSION_STAGE_AJOUTER,
                        ],
                        'resources' => [AnneeUniversitaire::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\SessionStage',
                    ],
                    [
                        'privileges' => [
                            StagePrivileges::STAGE_MODIFIER,
                        ],
                        'resources' => [SessionStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\SessionStage',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'session' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/session',
                    'defaults' => [
                        'controller' => SessionStageController::class,
                        'action' => SessionStageController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => SessionStageController::class,
                                'action' => SessionStageController::ACTION_AFFICHER,
                            ],
                        ],
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter[/:anneeUniversitaire]',
                            'constraints' => [
                                "anneeUniversitaire" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => SessionStageController::class,
                                'action' => SessionStageController::ACTION_AJOUTER,
                            ],
                        ],
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => SessionStageController::class,
                                'action' => SessionStageController::ACTION_MODIFIER,
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
                                'controller' => SessionStageController::class,
                                'action' => SessionStageController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'stages' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/stages',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'modifier-ordres' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier-ordres[/:sessionStage]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_MODIFIER_ORDRES_AFFECTATIONS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'recalculer-ordres' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/recalculer-ordres[/:sessionStage]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_RECALCULER_ORDRES_AFFECTATIONS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'periode-stage' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/periode-stage',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter[/:sessionStage]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_AJOUTER_PERIODE_STAGE
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:sessionStage/:date]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                        "date" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_MODIFIER_PERIODE_STAGE
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:sessionStage/:date]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                        "date" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_SUPPRIMER_PERIODE_STAGE
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],

                    'terrains' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/terrains',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:sessionStage]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_MODIFIER_PLACES_TERRAINS
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'importer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/importer[/:sessionStage]',
                                    'constraints' => [
                                        "sessionStage" => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SessionStageController::class,
                                        'action' => SessionStageController::ACTION_IMPORTER_PLACES_TERRAINS
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            SessionStageController::class => SessionStageControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            //Sessions de stages
            SessionStageSelectPicker::class => SelectPickerFactory::class,
            SessionStageEtatSelectPicker::class => SelectPickerFactory::class,
            SessionStageForm::class => SessionStageFormFactory::class,
            PeriodeStageForm::class => PeriodeStageFormFactory::class,
            SessionStageRechercheForm::class => SessionStageRechercheFormFactory::class,
            SessionStageFieldset::class => SessionStageFieldsetFactory::class,
            PeriodeStageFieldset::class => PeriodeStageFieldsetFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            SessionStageService::class => SessionStageServiceFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            SessionStageHydrator::class => SessionStageHydratorFactory::class,
            PeriodeStageHydrator::class => PeriodeStageHydratorFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
            SessionStageValidator::class => SessionStageValidatorFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            "sessionStage" => SessionStageViewHelper::class,
            'calendrierStage' => CalendrierStageViewHelper::class,
        ],
        'invokables' => [
        ],
        'factories' => [
            SessionStageViewHelper::class => SessionStageViewHelperFactory::class,
        ]
    ],
    'session_containers' => [
    ],
];
