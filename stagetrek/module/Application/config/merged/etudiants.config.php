<?php
//Gestion des étudiants et des groupes
use Application\Controller\Etudiant\DisponibiliteController;
use Application\Controller\Etudiant\EtudiantController;
use Application\Controller\Etudiant\Factory\DisponibiliteControllerFactory;
use Application\Controller\Etudiant\Factory\EtudiantControllerFactory;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Form\Etudiant\DisponibiliteForm;
use Application\Form\Etudiant\Element\EtudiantEtatSelectPicker;
use Application\Form\Etudiant\EtudiantForm;
use Application\Form\Etudiant\EtudiantRechercheForm;
use Application\Form\Etudiant\Factory\DisponibiliteFieldsetFactory;
use Application\Form\Etudiant\Factory\DisponibiliteFormFactory;
use Application\Form\Etudiant\Factory\DisponibiliteHydratorFactory;
use Application\Form\Etudiant\Factory\DisponibiliteValidatorFactory;
use Application\Form\Etudiant\Factory\EtudiantFieldsetFactory;
use Application\Form\Etudiant\Factory\EtudiantFormFactory;
use Application\Form\Etudiant\Factory\EtudiantHydratorFactory;
use Application\Form\Etudiant\Factory\EtudiantRechercheFormFactory;
use Application\Form\Etudiant\Factory\ImportEtudiantFormFactory;
use Application\Form\Etudiant\Fieldset\DisponibiliteFieldset;
use Application\Form\Etudiant\Fieldset\EtudiantFieldset;
use Application\Form\Etudiant\Hydrator\DisponibiliteHydrator;
use Application\Form\Etudiant\Hydrator\EtudiantHydrator;
use Application\Form\Etudiant\ImportEtudiantForm;
use Application\Form\Etudiant\Validator\DisponibiliteValidator;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Form\Preferences\Factory\PreferenceFieldsetFactory;
use Application\Form\Preferences\Factory\PreferenceFormFactory;
use Application\Form\Preferences\Factory\PreferenceHydratorFactory;
use Application\Form\Preferences\Factory\PreferenceValidatorFactory;
use Application\Form\Preferences\Fieldset\PreferenceFieldset;
use Application\Form\Preferences\Hydrator\PreferenceHydrator;
use Application\Form\Preferences\PreferenceForm;
use Application\Form\Preferences\Validator\PreferenceValidator;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Service\Etudiant\DisponibiliteService;
use Application\Service\Etudiant\EtudiantImportService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Etudiant\Factory\DisponibiliteServiceFactory;
use Application\Service\Etudiant\Factory\EtudiantImportServiceFactory;
use Application\Service\Etudiant\Factory\EtudiantServiceFactory;
use Application\Service\Preference\Factory\PreferenceServiceFactory;
use Application\Service\Preference\PreferenceService;
use Application\Validator\Import\EtudiantCsvImportValidator;
use Application\Validator\Import\Factory\AbstractImportCsvValidatorFactory;
use Application\View\Helper\Disponibilite\DisponibiliteViewHelper;
use Application\View\Helper\Etudiant\EtudiantViewHelper;
use Application\View\Helper\Etudiant\EtudiantViewHelperFactory;
use Application\View\Helper\Preferences\Factory\PreferenceViewHelperFactory;
use Application\View\Helper\Preferences\PreferenceViewHelper;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            //Gestions des etudiants
            PrivilegeController::class => [
                [
                    'controller' => EtudiantController::class,
                    'action' => [
                        EtudiantController::ACTION_INDEX,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_AFFICHER
                    ],
                    'assertion' => 'Assertion\\Etudiant',
                ],
                [
                    'controller' => EtudiantController::class,
                    'action' => [
                        EtudiantController::ACTION_AFFICHER,
                        EtudiantController::ACTION_AFFICHER_INFOS,
                        EtudiantController::ACTION_LISTER_STAGES,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_AFFICHER
                    ],
                    'assertion' => 'Assertion\\Etudiant',
                ],
                [
                    'controller' => EtudiantController::class,
                    'action' => [
                        EtudiantController::ACTION_AJOUTER,
                        EtudiantController::ACTION_IMPORTER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_AJOUTER
                    ],
                    'assertion' => 'Assertion\\Etudiant',
                ],
                [
                    'controller' => EtudiantController::class,
                    'action' => [
                        EtudiantController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_MODIFIER
                    ],
                    'assertion' => 'Assertion\\Etudiant',
                ],
                [
                    'controller' => EtudiantController::class,
                    'action' => [
                        EtudiantController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_SUPPRIMER
                    ],
                    'assertion' => 'Assertion\\Etudiant',

                ],
                /**
                 * Pages personnes des étudiants
                 */
                [
                    'controller' => EtudiantController::class,
                    'action' => [
                        EtudiantController::ACTION_MON_PROFIL,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_OWN_PROFIL_AFFICHER
                    ],
                    'assertion' => "Assertion\\Etudiant",
                ],

                //Disponibilités des étudiants
                [
                    'controller' => DisponibiliteController::class,
                    'action' => [
                        DisponibiliteController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::DISPONIBILITE_AFFICHER
                    ],
                    'assertion' => "Assertion\\Disponibilite",
                ],
                [
                    'controller' => DisponibiliteController::class,
                    'action' => [
                        DisponibiliteController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::DISPONIBILITE_AJOUTER
                    ],
                    'assertion' => "Assertion\\Disponibilite",
                ],
                [
                    'controller' => DisponibiliteController::class,
                    'action' => [
                        DisponibiliteController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::DISPONIBILITE_MODIFIER
                    ],
                    'assertion' => "Assertion\\Disponibilite",
                ],
                [
                    'controller' => DisponibiliteController::class,
                    'action' => [
                        DisponibiliteController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::DISPONIBILITE_SUPPRIMER
                    ],
                    'assertion' => "Assertion\\Disponibilite",
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Etudiant::RESOURCE_ID => [],
                Disponibilite::RESOURCE_ID => [],
                ArrayRessource::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            EtudiantPrivileges::ETUDIANT_AFFICHER,
                            EtudiantPrivileges::ETUDIANT_AJOUTER,
                            EtudiantPrivileges::ETUDIANT_MODIFIER,
                            EtudiantPrivileges::ETUDIANT_SUPPRIMER,
                        ],
                        'resources' => [Etudiant::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Etudiant',
                    ],
                    [
                        'privileges' => [
                            EtudiantPrivileges::DISPONIBILITE_MODIFIER,
                            EtudiantPrivileges::DISPONIBILITE_SUPPRIMER,
                        ],
                        'resources' => [Etudiant::RESOURCE_ID, Disponibilite::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Disponibilite',
                    ],
                    [
                        'privileges' => [
                            EtudiantPrivileges::DISPONIBILITE_AFFICHER,
                            EtudiantPrivileges::DISPONIBILITE_AJOUTER,
                        ],
                        'resources' => [Etudiant::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Disponibilite',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'etudiant' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/etudiant',
                    'defaults' => [
                        'controller' => EtudiantController::class,
                        'action' => EtudiantController::ACTION_INDEX,
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:etudiant]',
                            'constraints' => [
                                'etudiant' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => EtudiantController::class,
                                'action' => EtudiantController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'infos' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/infos[/:etudiant]',
                                    'constraints' => [
                                        'etudiant' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => EtudiantController::class,
                                        'action' => EtudiantController::ACTION_AFFICHER_INFOS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'stages' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/stages',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    //TODO: renommer seulements en lister losque les stages aurons leurs propres routes distinct de Etudiant
                                    'route' => '/lister[/:etudiant]',
                                    'constraints' => [
                                        'etudiant' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => EtudiantController::class,
                                        'action' => EtudiantController::ACTION_LISTER_STAGES,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => EtudiantController::class,
                                'action' => EtudiantController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'importer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/importer[/:groupe]',
                            'constraints' => [
                                'groupe' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => EtudiantController::class,
                                'action' => EtudiantController::ACTION_IMPORTER,
                            ],
                        ],
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:etudiant]',
                            'constraints' => [
                                'etudiant' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => EtudiantController::class,
                                'action' => EtudiantController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:etudiant]',
                            'constraints' => [
                                'etudiant' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => EtudiantController::class,
                                'action' => EtudiantController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                ],
            ],
            'mon-profil' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/mon-profil',
                    'defaults' => [
                        'controller' => EtudiantController::class,
                        'action' => EtudiantController::ACTION_MON_PROFIL,
                    ]
                ],
                'may_terminate' => true,
            ],
            /** Disponibilites */
            'disponibilite' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/disponibilite',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister[/:etudiant]',
                            'constraints' => [
                                'ajouter' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => DisponibiliteController::class,
                                'action' => DisponibiliteController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter[/:etudiant]',
                            'constraints' => [
                                'ajouter' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => DisponibiliteController::class,
                                'action' => DisponibiliteController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:disponibilite]',
                            'constraints' => [
                                'disponibilite' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => DisponibiliteController::class,
                                'action' => DisponibiliteController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:disponibilite]',
                            'constraints' => [
                                'disponibilite' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => DisponibiliteController::class,
                                'action' => DisponibiliteController::ACTION_SUPPRIMER,
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
            EtudiantController::class => EtudiantControllerFactory::class,
            DisponibiliteController::class => DisponibiliteControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            EtudiantForm::class => EtudiantFormFactory::class,
            EtudiantRechercheForm::class => EtudiantRechercheFormFactory::class,
            ImportEtudiantForm::class => ImportEtudiantFormFactory::class,
            PreferenceForm::class => PreferenceFormFactory::class,
            DisponibiliteForm::class => DisponibiliteFormFactory::class,

            // Fieldset
            EtudiantFieldset::class => EtudiantFieldsetFactory::class,
            PreferenceFieldset::class => PreferenceFieldsetFactory::class,
            DisponibiliteFieldset::class => DisponibiliteFieldsetFactory::class,
            EtudiantEtatSelectPicker::class => SelectPickerFactory::class,

        ],
    ],
    'hydrators' => [
        'factories' => [
            EtudiantHydrator::class => EtudiantHydratorFactory::class,
            PreferenceHydrator::class => PreferenceHydratorFactory::class,
            DisponibiliteHydrator::class => DisponibiliteHydratorFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            DisponibiliteService::class => DisponibiliteServiceFactory::class,
            EtudiantService::class => EtudiantServiceFactory::class,
            EtudiantImportService::class => EtudiantImportServiceFactory::class,
            PreferenceService::class => PreferenceServiceFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
            //Pour les imports
            EtudiantCsvImportValidator::class => AbstractImportCsvValidatorFactory::class,
            //Pour les formulaires
            PreferenceValidator::class => PreferenceValidatorFactory::class,
            DisponibiliteValidator::class => DisponibiliteValidatorFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'etudiant' => EtudiantViewHelper::class,
            'preference' => PreferenceViewHelper::class,
            'disponibilite' => DisponibiliteViewHelper::class,
        ],
        'invokables' => [
            'disponibilite' => DisponibiliteViewHelper::class,
        ],
        'factories' => [
            EtudiantViewHelper::class => EtudiantViewHelperFactory::class,
            PreferenceViewHelper::class => PreferenceViewHelperFactory::class,
        ]
    ],
];

