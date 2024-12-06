<?php

namespace Referentiel;
use Application\Controller\Parametre\Factory\NiveauEtudeControllerFactory;
use Application\Controller\Parametre\Factory\ParametreControllerFactory;
use Application\Controller\Parametre\Factory\ParametreCoutAffectationControllerFactory;
use Application\Controller\Parametre\Factory\ParametreCoutTerrainControllerFactory;
use Application\Controller\Parametre\NiveauEtudeController;
use Application\Controller\Parametre\ParametreController;
use Application\Controller\Parametre\ParametreCoutAffectationController;
use Application\Controller\Parametre\ParametreCoutTerrainController;
use Application\Entity\Db\NiveauEtude;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Form\Parametre\Element\NiveauEtudeSelectPicker;
use Application\Form\Parametre\Factory\NiveauEtudeFieldsetFactory;
use Application\Form\Parametre\Factory\NiveauEtudeFormFactory;
use Application\Form\Parametre\Factory\NiveauEtudeHydratorFactory;
use Application\Form\Parametre\Factory\ParametreCoutAffectationFieldsetFactory;
use Application\Form\Parametre\Factory\ParametreCoutAffectationFormFactory;
use Application\Form\Parametre\Factory\ParametreCoutTerrainFieldsetFactory;
use Application\Form\Parametre\Factory\ParametreCoutTerrainFormFactory;
use Application\Form\Parametre\Factory\ParametreCoutTerrainHydratorFactory;
use Application\Form\Parametre\Factory\ParametreFieldsetFactory;
use Application\Form\Parametre\Factory\ParametreFormFactory;
use Application\Form\Parametre\Fieldset\NiveauEtudeFieldset;
use Application\Form\Parametre\Fieldset\ParametreCoutAffectationFieldset;
use Application\Form\Parametre\Fieldset\ParametreCoutTerrainFieldset;
use Application\Form\Parametre\Fieldset\ParametreFieldset;
use Application\Form\Parametre\Hydrator\NiveauEtudeHydrator;
use Application\Form\Parametre\Hydrator\ParametreCoutTerrainHydrator;
use Application\Form\Parametre\NiveauEtudeForm;
use Application\Form\Parametre\ParametreCoutAffectationForm;
use Application\Form\Parametre\ParametreCoutTerrainForm;
use Application\Form\Parametre\ParametreForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\Service\Parametre\Factory\NiveauEtudeServiceFactory;
use Application\Service\Parametre\Factory\ParametreCoutAffectationServiceFactory;
use Application\Service\Parametre\Factory\ParametreCoutTerrainServiceFactory;
use Application\Service\Parametre\Factory\ParametreServiceFactory;
use Application\Service\Parametre\NiveauEtudeService;
use Application\Service\Parametre\ParametreCoutAffectationService;
use Application\Service\Parametre\ParametreCoutTerrainService;
use Application\Service\Parametre\ParametreService;
use Application\View\Helper\Parametres\Factory\NiveauEtudeViewHelperFactory;
use Application\View\Helper\Parametres\NiveauEtudeViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;
use UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => ParametreController::class,
                    'action' => [
                        ParametreController::ACTION_INDEX,
                        ParametreController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\Parametre',
                ],
                [
                    'controller' => ParametreController::class,
                    'action' => [
                        ParametreController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\Parametre',
                ],
                [
                    'controller' => ParametreCoutAffectationController::class,
                    'action' => [
                        ParametreCoutAffectationController::ACTION_INDEX,
                        ParametreCoutAffectationController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutAffectation',
                ],
                [
                    'controller' => ParametreCoutAffectationController::class,
                    'action' => [
                        ParametreCoutAffectationController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutAffectation',
                ],
                [
                    'controller' => ParametreCoutAffectationController::class,
                    'action' => [
                        ParametreCoutAffectationController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutAffectation',
                ],
                [
                    'controller' => ParametreCoutAffectationController::class,
                    'action' => [
                        ParametreCoutAffectationController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutAffectation',
                ],
                [
                    'controller' => ParametreCoutTerrainController::class,
                    'action' => [
                        ParametreCoutTerrainController::ACTION_INDEX,
                        ParametreCoutTerrainController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutTerrain',
                ],
                [
                    'controller' => ParametreCoutTerrainController::class,
                    'action' => [
                        ParametreCoutTerrainController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutTerrain',
                ],
                [
                    'controller' => ParametreCoutTerrainController::class,
                    'action' => [
                        ParametreCoutTerrainController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutTerrain',
                ],
                [
                    'controller' => ParametreCoutTerrainController::class,
                    'action' => [
                        ParametreCoutTerrainController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ParametreCoutTerrain',
                ],
                [
                    'controller' => NiveauEtudeController::class,
                    'action' => [
                        NiveauEtudeController::ACTION_INDEX,
                        NiveauEtudeController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::NIVEAU_ETUDE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\NiveauEtude',
                ],
                [
                    'controller' => NiveauEtudeController::class,
                    'action' => [NiveauEtudeController::ACTION_AJOUTER],
                    'privileges' => [
                        ParametrePrivileges::NIVEAU_ETUDE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\NiveauEtude',
                ],
                [
                    'controller' => NiveauEtudeController::class,
                    'action' => [NiveauEtudeController::ACTION_MODIFIER],
                    'privileges' => [
                        ParametrePrivileges::NIVEAU_ETUDE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\NiveauEtude',
                ],
                [
                    'controller' => NiveauEtudeController::class,
                    'action' => [NiveauEtudeController::ACTION_SUPPRIMER],
                    'privileges' => [
                        ParametrePrivileges::NIVEAU_ETUDE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\NiveauEtude',
                ],
            ],
        ],
//        Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Parametre::RESOURCE_ID => [],
                ParametreCoutAffectation::RESOURCE_ID => [],
                ParametreTerrainCoutAffectationFixe::RESOURCE_ID => [],
                NiveauEtude::RESOURCE_ID => [],
            ],
        ],

        'rule_providers' => [
            PrivilegeRuleProvider::class => [
                'allow' => [
                    [
                        'privileges' => [
                            ParametrePrivileges::PARAMETRE_AFFICHER,
                            ParametrePrivileges::PARAMETRE_AJOUTER,
                            ParametrePrivileges::PARAMETRE_MODIFIER,
                            ParametrePrivileges::PARAMETRE_SUPPRIMER,
                        ],
                        'resources' => [Parametre::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Parametre',
                    ],
                    [
                        'privileges' => [
                            ParametrePrivileges::PARAMETRE_AFFICHER,
                            ParametrePrivileges::PARAMETRE_AJOUTER,
                            ParametrePrivileges::PARAMETRE_MODIFIER,
                            ParametrePrivileges::PARAMETRE_SUPPRIMER,
                        ],
                        'resources' => [ParametreCoutAffectation::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ParametreCoutAffectation',
                    ],
                    [
                        'privileges' => [
                            ParametrePrivileges::PARAMETRE_AFFICHER,
                            ParametrePrivileges::PARAMETRE_AJOUTER,
                            ParametrePrivileges::PARAMETRE_MODIFIER,
                            ParametrePrivileges::PARAMETRE_SUPPRIMER,
                        ],
                        'resources' => [ParametreTerrainCoutAffectationFixe::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ParametreCoutTerrain',
                    ],
                    [
                        'privileges' => [
                            ParametrePrivileges::NIVEAU_ETUDE_AFFICHER,
                            ParametrePrivileges::NIVEAU_ETUDE_AJOUTER,
                            ParametrePrivileges::NIVEAU_ETUDE_MODIFIER,
                            ParametrePrivileges::NIVEAU_ETUDE_SUPPRIMER,
                        ],
                        'resources' => [NiveauEtude::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\NiveauEtude',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'parametre' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/parametre',
                    'defaults' => [
                        'controller' => ParametreController::class,
                        'action' => ParametreController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => ParametreController::class,
                                'action' => ParametreController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:parametre]',
                            'constraints' => [
                                'parametre' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ParametreController::class,
                                'action' => ParametreController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'cout-affectation' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cout-affectation',
                            'defaults' => [
                                'controller' => ParametreCoutAffectationController::class,
                                'action' => ParametreCoutAffectationController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => ParametreCoutAffectationController::class,
                                        'action' => ParametreCoutAffectationController::ACTION_LISTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => ParametreCoutAffectationController::class,
                                        'action' => ParametreCoutAffectationController::ACTION_AJOUTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:parametreCoutAffectation]',
                                    'constraints' => [
                                        'parametreCoutAffectation' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ParametreCoutAffectationController::class,
                                        'action' => ParametreCoutAffectationController::ACTION_MODIFIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:parametreCoutAffectation]',
                                    'constraints' => [
                                        'parametreCoutAffectation' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ParametreCoutAffectationController::class,
                                        'action' => ParametreCoutAffectationController::ACTION_SUPPRIMER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'cout-terrain' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cout-terrain',
                            'defaults' => [
                                'controller' => ParametreCoutTerrainController::class,
                                'action' => ParametreCoutTerrainController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => ParametreCoutTerrainController::class,
                                        'action' => ParametreCoutTerrainController::ACTION_LISTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => ParametreCoutTerrainController::class,
                                        'action' => ParametreCoutTerrainController::ACTION_AJOUTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:parametreTerrainCoutAffectationFixe]',
                                    'constraints' => [
                                        'parametreTerrainCoutAffectationFixe' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ParametreCoutTerrainController::class,
                                        'action' => ParametreCoutTerrainController::ACTION_MODIFIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:parametreTerrainCoutAffectationFixe]',
                                    'constraints' => [
                                        'parametreTerrainCoutAffectationFixe' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ParametreCoutTerrainController::class,
                                        'action' => ParametreCoutTerrainController::ACTION_SUPPRIMER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'niveau-etude' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/niveau-etude',
                            'defaults' => [
                                'controller' => NiveauEtudeController::class,
                                'action' => NiveauEtudeController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => NiveauEtudeController::class,
                                        'action' => NiveauEtudeController::ACTION_LISTER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => NiveauEtudeController::class,
                                        'action' => NiveauEtudeController::ACTION_AJOUTER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:niveauEtude]',
                                    'constraints' => [
                                        'niveauEtude' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => NiveauEtudeController::class,
                                        'action' => NiveauEtudeController::ACTION_MODIFIER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:niveauEtude]',
                                    'constraints' => [
                                        'niveauEtude' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => NiveauEtudeController::class,
                                        'action' => NiveauEtudeController::ACTION_SUPPRIMER,
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
            ParametreController::class => ParametreControllerFactory::class,
            ParametreCoutAffectationController::class => ParametreCoutAffectationControllerFactory::class,
            ParametreCoutTerrainController::class => ParametreCoutTerrainControllerFactory::class,
            NiveauEtudeController::class => NiveauEtudeControllerFactory::class,
        ],
    ],

    'form_elements' => [
        'factories' => [
            ParametreCoutAffectationForm::class => ParametreCoutAffectationFormFactory::class,
            ParametreCoutTerrainForm::class => ParametreCoutTerrainFormFactory::class,
            ParametreForm::class => ParametreFormFactory::class,

            ParametreCoutAffectationFieldset::class => ParametreCoutAffectationFieldsetFactory::class,
            ParametreCoutTerrainFieldset::class => ParametreCoutTerrainFieldsetFactory::class,
            ParametreFieldset::class => ParametreFieldsetFactory::class,

            NiveauEtudeForm::class => NiveauEtudeFormFactory::class,
            NiveauEtudeFieldset::class => NiveauEtudeFieldsetFactory::class,
            NiveauEtudeSelectPicker::class => SelectPickerFactory::class,

        ],
    ],

    'hydrators' => [
        'factories' => [
            ParametreCoutTerrainHydrator::class => ParametreCoutTerrainHydratorFactory::class,
            NiveauEtudeHydrator::class => NiveauEtudeHydratorFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            ParametreService::class => ParametreServiceFactory::class,
            ParametreCoutAffectationService::class => ParametreCoutAffectationServiceFactory::class,
            ParametreCoutTerrainService::class => ParametreCoutTerrainServiceFactory::class,
            NiveauEtudeService::class => NiveauEtudeServiceFactory::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'niveauEtude' => NiveauEtudeViewHelper::class,
        ],
        'invokables' => [
        ],
        'factories' => [
            NiveauEtudeViewHelper::class => NiveauEtudeViewHelperFactory::class,
        ],
    ],
];


