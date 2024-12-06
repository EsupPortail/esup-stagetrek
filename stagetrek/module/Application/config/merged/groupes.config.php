<?php
//Gestion des étudiants et des groupes
use Application\Controller\Groupe\Factory\GroupeControllerFactory;
use Application\Controller\Groupe\GroupeController;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Groupe\Factory\GroupeFieldsetFactory;
use Application\Form\Groupe\Factory\GroupeFormFactory;
use Application\Form\Groupe\Factory\GroupeHydratorFactory;
use Application\Form\Groupe\Factory\GroupeRechercheFormFactory;
use Application\Form\Groupe\Fieldset\GroupeFieldset;
use Application\Form\Groupe\GroupeForm;
use Application\Form\Groupe\GroupeRechercheForm;
use Application\Form\Groupe\Hydrator\GroupeHydrator;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Service\Groupe\Factory\GroupeServiceFactory;
use Application\Service\Groupe\GroupeService;
use Application\Validator\Actions\Factory\GroupeValidatorFactory;
use Application\Validator\Actions\GroupeValidator;
use Application\View\Helper\Etudiant\EtudiantViewHelper;
use Application\View\Helper\Groupe\Factory\GroupeViewHelperFactory;
use Application\View\Helper\Groupe\GroupeViewHelper;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

//Gestions des groupes des étudiants
return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => GroupeController::class,
                    'action' => [
                        GroupeController::ACTION_INDEX,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::GROUPE_AFFICHER,
                    ],
                ],
                [
                    'controller' => GroupeController::class,
                    'action' => [
                        GroupeController::ACTION_AFFICHER,
                        GroupeController::ACTION_AFFICHER_INFOS,
                        GroupeController::ACTION_LISTER_ETUDIANTS,
                        GroupeController::ACTION_LISTER_SESSIONS,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::GROUPE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\Groupe',
                ],
                [
                    'controller' => GroupeController::class,
                    'action' => [
                        GroupeController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::GROUPE_AJOUTER
                    ],
                    'assertion' => 'Assertion\\Groupe',
                ],
                [
                    'controller' => GroupeController::class,
                    'action' => [
                        GroupeController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::GROUPE_MODIFIER
                    ],
                    'assertion' => 'Assertion\\Groupe',
                ],
                [
                    'controller' => GroupeController::class,
                    'action' => [
                        GroupeController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::GROUPE_SUPPRIMER
                    ],
                    'assertion' => 'Assertion\\Groupe',
                ],
                [
                    'controller' => GroupeController::class,
                    'action' => [
                        GroupeController::ACTION_AJOUTER_ETUDIANTS,
                        GroupeController::ACTION_RETIRER_ETUDIANTS,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::GROUPE_ADMINISTRER_ETUDIANTS,
                    ],
                    'assertion' => 'Assertion\\Groupe',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Groupe::RESOURCE_ID => [],
                AnneeUniversitaire::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            EtudiantPrivileges::GROUPE_AFFICHER,
                            EtudiantPrivileges::GROUPE_MODIFIER,
                            EtudiantPrivileges::GROUPE_ADMINISTRER_ETUDIANTS,
                            EtudiantPrivileges::GROUPE_SUPPRIMER,
                        ],
                        'resources' => [Groupe::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Groupe',
                    ],
                    [
                        'privileges' => [
                            EtudiantPrivileges::GROUPE_AJOUTER,
                        ],
                        'resources' => [AnneeUniversitaire::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Groupe',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'groupe' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/groupe',
                    'defaults' => [
                        'controller' => GroupeController::class,
                        'action' => GroupeController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:groupe]',
                            'constraints' => [
                                'groupe' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => GroupeController::class,
                                'action' => GroupeController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'infos' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/infos[/:groupe]',
                                    'constraints' => [
                                        'groupe' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => GroupeController::class,
                                        'action' => GroupeController::ACTION_AFFICHER_INFOS,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter[/:anneeUniversitaire]',
                            'constraints' => [
                                'anneeUniversitaire' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => GroupeController::class,
                                'action' => GroupeController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier/:groupe',
                            'constraints' => [
                                'groupe' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => GroupeController::class,
                                'action' => GroupeController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer/:groupe',
                            'constraints' => [
                                'groupe' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => GroupeController::class,
                                'action' => GroupeController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'etudiants' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/etudiants',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister/:groupe',
                                    'constraints' => [
                                        'groupe' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => GroupeController::class,
                                        'action' => GroupeController::ACTION_LISTER_ETUDIANTS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter/:groupe[/:etudiant]',
                                    'constraints' => [
                                        'groupe' => '[0-9]+',
                                        'etudiant' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => GroupeController::class,
                                        'action' => GroupeController::ACTION_AJOUTER_ETUDIANTS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'retirer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/retirer/:groupe[/:etudiant]',
                                    'constraints' => [
                                        'groupe' => '[0-9]+',
                                        'etudiant' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => GroupeController::class,
                                        'action' => GroupeController::ACTION_RETIRER_ETUDIANTS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'sessions' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/sessions',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister/:groupe',
                                    'constraints' => [
                                        'groupe' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => GroupeController::class,
                                        'action' => GroupeController::ACTION_LISTER_SESSIONS,
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
            GroupeController::class => GroupeControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            //Elements
            GroupeSelectPicker::class => SelectPickerFactory::class,

            GroupeForm::class => GroupeFormFactory::class,
            GroupeRechercheForm::class => GroupeRechercheFormFactory::class,
            // Fieldset
            GroupeFieldset::class => GroupeFieldsetFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            GroupeHydrator::class => GroupeHydratorFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            GroupeService::class => GroupeServiceFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
            GroupeValidator::class => GroupeValidatorFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'etudiant' => EtudiantViewHelper::class,
            'groupe' => GroupeViewHelper::class,
        ],
        'invokables' => [
        ],
        'factories' => [
            GroupeViewHelper::class => GroupeViewHelperFactory::class,
        ]
    ],
];

