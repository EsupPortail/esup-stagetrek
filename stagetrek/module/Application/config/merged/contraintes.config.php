<?php

use Application\Controller\Contrainte\ContrainteCursusController;
use Application\Controller\Contrainte\ContrainteCursusEtudiantController;
use Application\Controller\Contrainte\Factory\ContrainteCursusControllerFactory;
use Application\Controller\Contrainte\Factory\ContrainteCursusEtudiantControllerFactory;
use Application\Controller\Etudiant\EtudiantController;
use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Etudiant;
use Application\Form\Contrainte\ContrainteCursusEtudiantForm;
use Application\Form\Contrainte\ContrainteCursusForm;
use Application\Form\Contrainte\Element\ContrainteCursusPorteeSelectPicker;
use Application\Form\Contrainte\Element\ContrainteCursusSelectPicker;
use Application\Form\Contrainte\Factory\ContrainteCursusEtudiantFieldsetFactory;
use Application\Form\Contrainte\Factory\ContrainteCursusEtudiantFormFactory;
use Application\Form\Contrainte\Factory\ContrainteCursusFieldsetFactory;
use Application\Form\Contrainte\Factory\ContrainteCursusFormFactory;
use Application\Form\Contrainte\Factory\ContrainteCursusHydratorFactory;
use Application\Form\Contrainte\Factory\ContrainteCursusValidatorFactory;
use Application\Form\Contrainte\Fieldset\ContrainteCursusEtudiantFieldset;
use Application\Form\Contrainte\Fieldset\ContrainteCursusFieldset;
use Application\Form\Contrainte\Hydrator\ContrainteCursusHydrator;
use Application\Form\Contrainte\Validator\ContrainteCursusValidator;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Contrainte\ContrainteCursusService;
use Application\Service\Contrainte\Factory\ContrainteCursusEtudiantServiceFactory;
use Application\Service\Contrainte\Factory\ContrainteCursusServiceFactory;
use Application\View\Helper\ContrainteCursus\ContrainteCursusEtudiantViewHelper;
use Application\View\Helper\ContrainteCursus\ContrainteCursusViewHelper;
use Application\View\Helper\ContrainteCursus\Factory\ContrainteCursusEtudiantViewHelperFactory;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                //Contraintes de cursus
                [
                    'controller' => ContrainteCursusController::class,
                    'action' => [
                        ContrainteCursusController::ACTION_INDEX,
                        ContrainteCursusController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ContrainteCursus',
                ],
                [
                    'controller' => ContrainteCursusController::class,
                    'action' => [
                        ContrainteCursusController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AJOUTER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursus',
                ],
                [
                    'controller' => ContrainteCursusController::class,
                    'action' => [
                        ContrainteCursusController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_MODIFIER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursus',
                ],
                [
                    'controller' => ContrainteCursusController::class,
                    'action' => [
                        ContrainteCursusController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_SUPPRIMER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursus',
                ],



                /**
                 *  Contrainte du cursus
                 */
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_AFFICHER,
                        ContrainteCursusEtudiantController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTES_AFFICHER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTE_MODIFIER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_VALIDER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTE_VALIDER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_INVALIDER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTE_INVALIDER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_ACTIVER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTE_INVALIDER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_ACTIVER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTE_ACTIVER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
                [
                    'controller' => ContrainteCursusEtudiantController::class,
                    'action' => [
                        ContrainteCursusEtudiantController::ACTION_DESACTIVER,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_CONTRAINTE_DESACTIVER
                    ],
                    'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions

        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                ContrainteCursus::RESOURCE_ID => [],
                ContrainteCursusEtudiant::RESOURCE_ID => [],
                Etudiant::RESOURCE_ID => [],
                ArrayRessource::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AFFICHER,
                            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AJOUTER,
                            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_MODIFIER,
                            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_SUPPRIMER
                        ],
                        'resources' => [ContrainteCursus::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ContrainteCursus',
                    ],
                    [
                        'privileges' => [
                            EtudiantPrivileges::ETUDIANT_CONTRAINTE_MODIFIER,
                            EtudiantPrivileges::ETUDIANT_CONTRAINTE_VALIDER,
                            EtudiantPrivileges::ETUDIANT_CONTRAINTE_INVALIDER,
                            EtudiantPrivileges::ETUDIANT_CONTRAINTE_ACTIVER,
                            EtudiantPrivileges::ETUDIANT_CONTRAINTE_DESACTIVER,
                        ],
                        'resources' => [ContrainteCursusEtudiant::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                    ],
                    [
                        'privileges' => [
                            EtudiantPrivileges::ETUDIANT_CONTRAINTES_AFFICHER,
                        ],
                        'resources' => [ContrainteCursusEtudiant::RESOURCE_ID, Etudiant::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ContrainteCursusEtudiant',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'config' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/config',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'contraintes-cursus' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/contraintes-cursus',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => ContrainteCursusController::class,
                                        'action' => ContrainteCursusController::ACTION_LISTER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                        ],
                    ],
                    'contrainte-cursus' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/contrainte-cursus',
                            'defaults' => [
                                'controller' => ContrainteCursusController::class,
                                'action' => ContrainteCursusController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => ContrainteCursusController::class,
                                        'action' => ContrainteCursusController::ACTION_AJOUTER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:contrainteCursus]',
                                    'constraints' => [
                                        'contrainteCursus' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContrainteCursusController::class,
                                        'action' => ContrainteCursusController::ACTION_MODIFIER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:contrainteCursus]',
                                    'constraints' => [
                                        'contrainteCursus' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContrainteCursusController::class,
                                        'action' => ContrainteCursusController::ACTION_SUPPRIMER,
                                    ],
                                    'may_terminate' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'etudiant' => [
                'child_routes' => [
                    'lister-contraintes' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister-contraintes[/:etudiant]',
                            'constraints' => [
                                'etudiant' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ContrainteCursusEtudiantController::class,
                                'action' => ContrainteCursusEtudiantController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'cursus' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cursus',
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'afficher' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/afficher[/:etudiant]',
                                    'defaults' => [
                                        'controller' => ContrainteCursusEtudiantController::class,
                                        'action' => ContrainteCursusEtudiantController::ACTION_LISTER,
                                    ]
                                ],
                            ],
                            'contrainte' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/contrainte',
                                    'defaults' => [
                                        'controller' => EtudiantController::class,
                                        'action' => EtudiantController::ACTION_INDEX,
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'afficher' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/afficher[/:contrainteCursusEtudiant]',
                                            'constraints' => [
                                                'contrainteCursusEtudiant' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContrainteCursusEtudiantController::class,
                                                'action' => ContrainteCursusEtudiantController::ACTION_AFFICHER,
                                            ],
                                        ],
                                    ],
                                    'modifier' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/modifier[/:contrainteCursusEtudiant]',
                                            'constraints' => [
                                                'contrainteCursusEtudiant' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContrainteCursusEtudiantController::class,
                                                'action' => ContrainteCursusEtudiantController::ACTION_MODIFIER,
                                            ],
                                        ],
                                    ],
                                    'valider' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/valider[/:contrainteCursusEtudiant]',
                                            'constraints' => [
                                                'contrainteCursusEtudiant' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContrainteCursusEtudiantController::class,
                                                'action' => ContrainteCursusEtudiantController::ACTION_VALIDER,
                                            ],
                                        ],
                                    ],
                                    'invalider' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/invalider[/:contrainteCursusEtudiant]',
                                            'constraints' => [
                                                'contrainteCursusEtudiant' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContrainteCursusEtudiantController::class,
                                                'action' => ContrainteCursusEtudiantController::ACTION_INVALIDER,
                                            ],
                                        ],
                                    ],
                                    'activer' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/activer[/:contrainteCursusEtudiant]',
                                            'constraints' => [
                                                'contrainteCursusEtudiant' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContrainteCursusEtudiantController::class,
                                                'action' => ContrainteCursusEtudiantController::ACTION_ACTIVER,
                                            ],
                                        ],
                                    ],
                                    'desactiver' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/desactiver[/:contrainteCursusEtudiant]',
                                            'constraints' => [
                                                'contrainteCursusEtudiant' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContrainteCursusEtudiantController::class,
                                                'action' => ContrainteCursusEtudiantController::ACTION_DESACTIVER,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            ContrainteCursusService::class => ContrainteCursusServiceFactory::class,
            ContrainteCursusEtudiantService::class => ContrainteCursusEtudiantServiceFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => array(
            ContrainteCursusEtudiantForm::class => ContrainteCursusEtudiantFormFactory::class,
            ContrainteCursusEtudiantFieldset::class => ContrainteCursusEtudiantFieldsetFactory::class,

            ContrainteCursusForm::class => ContrainteCursusFormFactory::class,
            ContrainteCursusFieldset::class => ContrainteCursusFieldsetFactory::class,
            ContrainteCursusSelectPicker::class => SelectPickerFactory::class,
            ContrainteCursusPorteeSelectPicker::class => SelectPickerFactory::class,
        ),
    ],
    'hydrators' => [
        'factories' => [
            ContrainteCursusHydrator::class => ContrainteCursusHydratorFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
            ContrainteCursusValidator::class => ContrainteCursusValidatorFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            ContrainteCursusController::class => ContrainteCursusControllerFactory::class,
            ContrainteCursusEtudiantController::class => ContrainteCursusEtudiantControllerFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'contrainteCursus' => ContrainteCursusEtudiantViewHelper::class,
            'administrationContrainteCursus' => ContrainteCursusViewHelper::class,
        ],
        'invokables' => [
            'administrationContrainteCursus' => ContrainteCursusViewHelper::class,
        ],
        'factories' => [
            ContrainteCursusEtudiantViewHelper::class => ContrainteCursusEtudiantViewHelperFactory::class,
        ]
    ],
];

