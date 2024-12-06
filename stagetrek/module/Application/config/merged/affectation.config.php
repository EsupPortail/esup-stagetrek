<?php

use Application\Controller\Affectation\AffectationController;
use Application\Controller\Affectation\Factory\AffectationControllerFactory;
use Application\Controller\Affectation\Factory\ProcedureAffectationControllerFactory;
use Application\Controller\Affectation\ProcedureAffectationController;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\ProcedureAffectation;
use Application\Entity\Db\SessionStage;
use Application\Form\Affectation\AffectationStageForm;
use Application\Form\Affectation\Factory\AffectationStageFieldsetFactory;
use Application\Form\Affectation\Factory\AffectationStageFormFactory;
use Application\Form\Affectation\Factory\AffectationStageHydratorFactory;
use Application\Form\Affectation\Factory\ProcedureAffectationFieldsetFactory;
use Application\Form\Affectation\Factory\ProcedureAffectationFormFactory;
use Application\Form\Affectation\Fieldset\AffectationStageFieldset;
use Application\Form\Affectation\Fieldset\ProcedureAffectationFieldset;
use Application\Form\Affectation\Hydrator\AffectationStageHydrator;
use Application\Form\Affectation\ProcedureAffectationForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\StagePrivileges;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Affectation\Algorithmes\AlgoAleatoire;
use Application\Service\Affectation\Algorithmes\AlgoScoreV1;
use Application\Service\Affectation\Algorithmes\AlgoScoreV2;
use Application\Service\Affectation\Factory\AbstractAlgorithmeAffectationFactory;
use Application\Service\Affectation\Factory\AffectationStageServiceFactory;
use Application\Service\Affectation\Factory\AlgoAleatoireFactory;
use Application\Service\Affectation\Factory\ProcedureAffectationServiceFactory;
use Application\Service\Affectation\ProcedureAffectationService;
use Application\View\Helper\Affectation\AffectationViewHelper;
use Application\View\Helper\Affectation\Factory\AffectationViewHelperFactory;
use Application\View\Helper\Affectation\Factory\ProcedureAffectationViewHelperFactory;
use Application\View\Helper\Affectation\ProcedureAffectationViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => AffectationController::class,
                    'action' => [
                        AffectationController::ACTION_LISTER,
                        AffectationController::ACTION_AFFICHER,
                        AffectationController::ACTION_EXPORTER,
                    ],
                    'privileges' => [
                        StagePrivileges::AFFECTATION_AFFICHER
                    ],
                    'assertion' => 'Assertion\\AffectationStage',
                ],
                [
                    'controller' => AffectationController::class,
                    'action' => [
                        AffectationController::ACTION_MODIFIER,
                        AffectationController::ACTION_MODIFIER_AFFECTATIONS,
                    ],
                    'privileges' => [
                        StagePrivileges::AFFECTATION_MODIFIER
                    ],
                    'assertion' => 'Assertion\\AffectationStage',
                ],
                [
                    'controller' => AffectationController::class,
                    'action' => [
                        AffectationController::ACTION_CALCULER_AFFECTATIONS,
                    ],
                    'privileges' => [
                        StagePrivileges::AFFECTATION_RUN_PROCEDURE
                    ],
                    'assertion' => 'Assertion\\AffectationStage',
                ],
                [
                    'controller' => ProcedureAffectationController::class,
                    'action' => [
                        ProcedureAffectationController::ACTION_INDEX,
                        ProcedureAffectationController::ACTION_LISTER,
                        ProcedureAffectationController::ACTION_AFFICHER,
                    ],
                    'privileges' => [
                        StagePrivileges::PROCEDURE_AFFICHER
                    ],
                    'assertion' => 'Assertion\\ProcedureAffectation',
                ],
                [
                    'controller' => ProcedureAffectationController::class,
                    'action' => [
                        ProcedureAffectationController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        StagePrivileges::PROCEDURE_MODIFIER
                    ],
                    'assertion' => 'Assertion\\ProcedureAffectation',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                AffectationStage::RESOURCE_ID => [],
                SessionStage::RESOURCE_ID => [],
                ProcedureAffectation::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            StagePrivileges::AFFECTATION_AFFICHER,
                            StagePrivileges::AFFECTATION_AJOUTER,
                            StagePrivileges::AFFECTATION_MODIFIER,
                            StagePrivileges::AFFECTATION_SUPPRIMER,
                            StagePrivileges::AFFECTATION_RUN_PROCEDURE,
                            StagePrivileges::AFFECTATION_PRE_VALIDER,
                            StagePrivileges::COMMISSION_VALIDER_AFFECTATIONS
                        ],
                        'resources' => [AffectationStage::RESOURCE_ID, SessionStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\AffectationStage',
                    ],
                    [
                        'privileges' => [
                            StagePrivileges::PROCEDURE_AFFICHER,
                            StagePrivileges::PROCEDURE_MODIFIER,
                        ],
                        'resources' => [ProcedureAffectation::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ProcedureAffectation',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'affectation' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/affectation',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:affectationStage]',
                            'constraints' => [
                                "affectationStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AffectationController::class,
                                'action' => AffectationController::ACTION_AFFICHER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:affectationStage]',
                            'constraints' => [
                                "affectationStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AffectationController::class,
                                'action' => AffectationController::ACTION_MODIFIER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'affectations' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/affectations',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AffectationController::class,
                                'action' => AffectationController::ACTION_LISTER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AffectationController::class,
                                'action' => AffectationController::ACTION_MODIFIER_AFFECTATIONS
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'exporter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/exporter[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AffectationController::class,
                                'action' => AffectationController::ACTION_EXPORTER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'calculer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/calculer[/:sessionStage]',
                            'constraints' => [
                                "sessionStage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AffectationController::class,
                                'action' => AffectationController::ACTION_CALCULER_AFFECTATIONS
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'parametre' => [
                'child_routes' => [
                    'procedure-affectation' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/procedure-affectation',
                            'defaults' => [
                                'controller' => ProcedureAffectationController::class,
                                'action' => ProcedureAffectationController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => ProcedureAffectationController::class,
                                        'action' => ProcedureAffectationController::ACTION_LISTER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'afficher' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter[/:procedureAffectation]',
                                    'constraints' => [
                                        'procedureAffectation' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ProcedureAffectationController::class,
                                        'action' => ProcedureAffectationController::ACTION_AFFICHER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:procedureAffectation]',
                                    'constraints' => [
                                        'procedureAffectation' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ProcedureAffectationController::class,
                                        'action' => ProcedureAffectationController::ACTION_MODIFIER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AffectationController::class => AffectationControllerFactory::class,
            ProcedureAffectationController::class => ProcedureAffectationControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            AffectationStageForm::class => AffectationStageFormFactory::class,
            AffectationStageFieldset::class => AffectationStageFieldsetFactory::class,
            ProcedureAffectationForm::class => ProcedureAffectationFormFactory::class,
            ProcedureAffectationFieldset::class => ProcedureAffectationFieldsetFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            AffectationStageService::class => AffectationStageServiceFactory::class,
            ProcedureAffectationService::class => ProcedureAffectationServiceFactory::class,
            AlgoScoreV1::class => AbstractAlgorithmeAffectationFactory::class,
            AlgoScoreV2::class => AbstractAlgorithmeAffectationFactory::class,
            AlgoAleatoire::class => AlgoAleatoireFactory::class,
        ],
        'abstract_factories' => [
            AbstractAlgorithmeAffectationFactory::class,
        ]
    ],
    'hydrators' => [
        'factories' => [
            AffectationStageHydrator::class => AffectationStageHydratorFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'affectation' => AffectationViewHelper::class,
            'procedureAffectation' => ProcedureAffectationViewHelper::class,
        ],
        'invokables' => [
        ],
        'factories' => [
            AffectationViewHelper::class => AffectationViewHelperFactory::class,
            ProcedureAffectationViewHelper::class => ProcedureAffectationViewHelperFactory::class,
        ]
    ],
    'session_containers' => [
    ],
];
