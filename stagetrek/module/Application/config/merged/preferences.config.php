<?php
//Gestions des stages
use Application\Controller\Preference\Factory\PreferenceControllerFactory;
use Application\Controller\Preference\PreferenceController;
use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Form\Preferences\Factory\PreferenceFieldsetFactory;
use Application\Form\Preferences\Factory\PreferenceFormFactory;
use Application\Form\Preferences\Factory\PreferenceHydratorFactory;
use Application\Form\Preferences\Fieldset\PreferenceFieldset;
use Application\Form\Preferences\Hydrator\PreferenceHydrator;
use Application\Form\Preferences\PreferenceForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Service\Preference\Factory\PreferenceServiceFactory;
use Application\Service\Preference\PreferenceService;
use Application\View\Helper\Preferences\Factory\PreferenceViewHelperFactory;
use Application\View\Helper\Preferences\PreferenceViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => PreferenceController::class,
                    'action' => [
                        PreferenceController::ACTION_LISTER,
                        PreferenceController::ACTION_LISTER_PLACES,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::PREFERENCE_AFFICHER,
                        EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\Preference',
                ],
                [
                    'controller' => PreferenceController::class,
                    'action' => [
                        PreferenceController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::PREFERENCE_AJOUTER,
                        EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AJOUTER
                    ],
                    'assertion' => 'Assertion\\Preference',
                ],
                [
                    'controller' => PreferenceController::class,
                    'action' => [
                        PreferenceController::ACTION_MODIFIER,
                        PreferenceController::ACTION_MODIFIER_RANG,
                        PreferenceController::ACTION_MODIFIER_PREFERENCES,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::PREFERENCE_MODIFIER,
                        EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_MODIFIER
                    ],
                    'assertion' => 'Assertion\\Preference',
                ],
                [
                    'controller' => PreferenceController::class,
                    'action' => [
                        PreferenceController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::PREFERENCE_SUPPRIMER,
                        EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_SUPPRIMER
                    ],
                    'assertion' => 'Assertion\\Preference',
                ],
            ],
        ],
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Preference::RESOURCE_ID => [],
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
                            EtudiantPrivileges::PREFERENCE_AFFICHER,
                            EtudiantPrivileges::PREFERENCE_AJOUTER,
                            EtudiantPrivileges::PREFERENCE_MODIFIER,
                            EtudiantPrivileges::PREFERENCE_SUPPRIMER,
                            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AFFICHER,
                            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_AJOUTER,
                            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_MODIFIER,
                            EtudiantPrivileges::ETUDIANT_OWN_PREFERENCES_SUPPRIMER,
                        ],
                        'resources' => [Preference::RESOURCE_ID, Stage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Preference',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'preferences' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/preferences',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister[/:stage]',
                            'constraints' => [
                                "stage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_LISTER
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
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_MODIFIER_PREFERENCES
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'preference' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/preference',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter[/:stage]',
                            'constraints' => [
                                "stage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_AJOUTER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:preference]',
                            'constraints' => [
                                "preference" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_MODIFIER
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier-rang' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier-rang[/:preference/:rang]',
                            'constraints' => [
                                "preference" => '[0-9]+',
                                "rang" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_MODIFIER_RANG
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'lister-places' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister-places[/:stage]',
                            'constraints' => [
                                "stage" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_LISTER_PLACES
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:preference]',
                            'constraints' => [
                                "preference" => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => PreferenceController::class,
                                'action' => PreferenceController::ACTION_SUPPRIMER
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
            PreferenceController::class => PreferenceControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            PreferenceForm::class => PreferenceFormFactory::class,
            PreferenceFieldset::class => PreferenceFieldsetFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            PreferenceService::class => PreferenceServiceFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            PreferenceHydrator::class => PreferenceHydratorFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'preference' => PreferenceViewHelper::class,
        ],
        'invokables' => [
        ],
        'factories' => [
            PreferenceViewHelper::class => PreferenceViewHelperFactory::class,
        ]
    ],
    'session_containers' => [
    ],

];
