<?php

use Application\Controller\AnneeUniversitaire\AnneeUniversitaireController;
use Application\Controller\AnneeUniversitaire\Factory\AnneeUniversitaireControllerFactory;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Form\Annees\AnneeUniversitaireForm;
use Application\Form\Annees\Element\AnneeUniversitaireEtatSelectPicker;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Annees\Factory\AnneeUniversitaireFieldsetFactory;
use Application\Form\Annees\Factory\AnneeUniversitaireFormFactory;
use Application\Form\Annees\Factory\AnneeUniversitaireHydratorFactory;
use Application\Form\Annees\Fieldset\AnneeUniversitaireFieldset;
use Application\Form\Annees\Hydrator\AnneeUniversitaireHydrator;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\AnneePrivileges;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\AnneeUniversitaire\Factory\AnneeUniversitaireServiceFactory;
use Application\View\Helper\Annees\AnneeUniversitaireViewHelper;
use Application\View\Helper\Stages\CalendrierStageViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                /** Calendrier des stages */
                [
                    'controller' => AnneeUniversitaireController::class,
                    'action' => [
                        AnneeUniversitaireController::ACTION_INDEX,
                        AnneeUniversitaireController::ACTION_LISTER,
                        AnneeUniversitaireController::ACTION_AFFICHER,
                        AnneeUniversitaireController::ACTION_AFFICHER_INFOS,
                        AnneeUniversitaireController::ACTION_AFFICHER_CALENDRIER,
                    ],
                    'privileges' => [
                        AnneePrivileges::ANNEE_UNIVERSITAIRE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\AnneeUniversitaire',
                ],
                [
                    'controller' => AnneeUniversitaireController::class,
                    'action' => [AnneeUniversitaireController::ACTION_AJOUTER],
                    'privileges' => [
                        AnneePrivileges::ANNEE_UNIVERSITAIRE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\AnneeUniversitaire',
                ],
                [
                    'controller' => AnneeUniversitaireController::class,
                    'action' => [AnneeUniversitaireController::ACTION_MODIFIER],
                    'privileges' => [
                        AnneePrivileges::ANNEE_UNIVERSITAIRE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\AnneeUniversitaire',
                ],
                [
                    'controller' => AnneeUniversitaireController::class,
                    'action' => [AnneeUniversitaireController::ACTION_SUPPRIMER],
                    'privileges' => [
                        AnneePrivileges::ANNEE_UNIVERSITAIRE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\AnneeUniversitaire',
                ],
                [
                    'controller' => AnneeUniversitaireController::class,
                    'action' => [AnneeUniversitaireController::ACTION_VALIDER],
                    'privileges' => [
                        AnneePrivileges::ANNEE_UNIVERSITAIRE_VALIDER,
                    ],
                    'assertion' => 'Assertion\\AnneeUniversitaire',
                ],
                [
                    'controller' => AnneeUniversitaireController::class,
                    'action' => [AnneeUniversitaireController::ACTION_DEVEROUILLER],
                    'privileges' => [
                        AnneePrivileges::ANNEE_UNIVERSITAIRE_DEVERROUILLER,
                    ],
                    'assertion' => 'Assertion\\AnneeUniversitaire',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
               AnneeUniversitaire::RESOURCE_ID => []
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            AnneePrivileges::ANNEE_UNIVERSITAIRE_AFFICHER,
                            AnneePrivileges::ANNEE_UNIVERSITAIRE_AJOUTER,
                            AnneePrivileges::ANNEE_UNIVERSITAIRE_MODIFIER,
                            AnneePrivileges::ANNEE_UNIVERSITAIRE_SUPPRIMER,
                            AnneePrivileges::ANNEE_UNIVERSITAIRE_VALIDER,
                            AnneePrivileges::ANNEE_UNIVERSITAIRE_DEVERROUILLER
                        ],
                        'resources' => [AnneeUniversitaire::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\AnneeUniversitaire',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'annee' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/annee',
                    'defaults' => [
                        'controller' => AnneeUniversitaireController::class,
                        'action' => AnneeUniversitaireController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:anneeUniversitaire]',
                            'constraints' => [
                                'anneeUniversitaire' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'infos' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/infos[/:anneeUniversitaire]',
                                    'constraints' => [
                                        'anneeUniversitaire' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => AnneeUniversitaireController::class,
                                        'action' => AnneeUniversitaireController::ACTION_AFFICHER_INFOS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'calendrier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/calendrier[/:anneeUniversitaire]',
                                    'constraints' => [
                                        'anneeUniversitaire' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => AnneeUniversitaireController::class,
                                        'action' => AnneeUniversitaireController::ACTION_AFFICHER_CALENDRIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:anneeUniversitaire]',
                            'constraints' => [
                                'anneeUniversitaire' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:anneeUniversitaire]',
                            'constraints' => [
                                'anneeUniversitaire' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'valider' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/valider[/:anneeUniversitaire]',
                            'constraints' => [
                                'anneeUniversitaire' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_VALIDER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'deverouiller' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/deverouiller[/:anneeUniversitaire]',
                            'constraints' => [
                                'anneeUniversitaire' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => AnneeUniversitaireController::class,
                                'action' => AnneeUniversitaireController::ACTION_DEVEROUILLER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            AnneeUniversitaireController::class => AnneeUniversitaireControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            AnneeUniversitaireSelectPicker::class => SelectPickerFactory::class,
            AnneeUniversitaireForm::class => AnneeUniversitaireFormFactory::class,
            AnneeUniversitaireFieldset::class => AnneeUniversitaireFieldsetFactory::class,
            AnneeUniversitaireEtatSelectPicker::class => SelectPickerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            AnneeUniversitaireService::class => AnneeUniversitaireServiceFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            AnneeUniversitaireHydrator::class => AnneeUniversitaireHydratorFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [

        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'calendrierStage' => CalendrierStageViewHelper::class,
            'anneeUniversitaire' => AnneeUniversitaireViewHelper::class,
        ],
        'invokables' => [
            CalendrierStageViewHelper::class => CalendrierStageViewHelper::class,
            AnneeUniversitaireViewHelper::class => AnneeUniversitaireViewHelper::class,
        ],
        'factories' => [
        ]
    ],
];
