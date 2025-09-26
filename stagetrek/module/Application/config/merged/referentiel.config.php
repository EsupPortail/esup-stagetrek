<?php

namespace Referentiel;
use Application\Controller\Referentiel\Factory\ReferentielControllerFactory;
use Application\Controller\Referentiel\Factory\ReferentielPromoControllerFactory;
use Application\Controller\Referentiel\Factory\SourceControllerFactory;
use Application\Controller\Referentiel\ReferentielController;
use Application\Controller\Referentiel\ReferentielPromoController;
use Application\Controller\Referentiel\SourceController;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Form\Referentiel\CSVImportEtudiantsForm;
use Application\Form\Referentiel\Element\ReferentielPromoSelectPicker;
use Application\Form\Referentiel\Element\SourceSelectPicker;
use Application\Form\Referentiel\Factory\CSVImportEtudiantsFormFactory;
use Application\Form\Referentiel\Factory\ImportEtudiantsHydratorFactory;
use Application\Form\Referentiel\Factory\ReferentielImportEtudiantsFormFactory;
use Application\Form\Referentiel\Factory\ReferentielPormoHydratorFactory;
use Application\Form\Referentiel\Factory\ReferentielPromoFieldsetFactory;
use Application\Form\Referentiel\Factory\ReferentielPromoFormFactory;
use Application\Form\Referentiel\Factory\SourceFieldsetFactory;
use Application\Form\Referentiel\Factory\SourceFormFactory;
use Application\Form\Referentiel\Fieldset\ReferentielPromoFieldset;
use Application\Form\Referentiel\Fieldset\SourceFieldset;
use Application\Form\Referentiel\Hydrator\CSVImportEtudiantsHydrator;
use Application\Form\Referentiel\Hydrator\ReferentielImportEtudiantsHydrator;
use Application\Form\Referentiel\Hydrator\ReferentielPromoHydrator;
use Application\Form\Referentiel\LDAPImportEtudiantsForm;
use Application\Form\Referentiel\ReferentielImportEtudiantsForm;
use Application\Form\Referentiel\ReferentielPromoForm;
use Application\Form\Referentiel\SourceForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Application\Provider\Privilege\ReferentielPrivilege;
use Application\Service\Referentiel\CsvEtudiantService;
use Application\Service\Referentiel\Factory\CSVEtudiantServiceFactory;
use Application\Service\Referentiel\Factory\LdapEtudiantServiceFactory;
use Application\Service\Referentiel\Factory\RechercheEtudiantLocalServiceFactory;
use Application\Service\Referentiel\Factory\ReferentielDbImportEtudiantServiceFactory;
use Application\Service\Referentiel\Factory\ReferentielPromoServiceFactory;
use Application\Service\Referentiel\Factory\MultipleReferentielsEtudiantsServiceFactory;
use Application\Service\Referentiel\Factory\SourceServiceFactory;
use Application\Service\Referentiel\LdapEtudiantService;
use Application\Service\Referentiel\LocalEtudiantService;
use Application\Service\Referentiel\ReferentielDbImportEtudiantService;
use Application\Service\Referentiel\ReferentielPromoService;
use Application\Service\Referentiel\MultipleReferentielEtudiantsService;
use Application\Service\Referentiel\SourceService;
use Application\View\Helper\Referentiel\ReferentielPromoViewHelper;
use Application\View\Helper\Referentiel\SourceViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;
use UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => ReferentielController::class,
                    'action' => [
                        ReferentielController::ACTION_RECHERCHER_ETUDIANT,
                    ],
                    'privileges' => [
                        EtudiantPrivileges::ETUDIANT_AJOUTER,
                    ],
                ],
                [
                    'controller' => SourceController::class,
                    'action' => [
                        SourceController::ACTION_INDEX,
                        SourceController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_SOURCE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\Source',
                ],
                [
                    'controller' => SourceController::class,
                    'action' => [
                        SourceController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_SOURCE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\Source',
                ],

                [
                    'controller' => SourceController::class,
                    'action' => [
                        SourceController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_SOURCE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\Source',
                ],
                [
                    'controller' => SourceController::class,
                    'action' => [
                        SourceController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_SOURCE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\Source',
                ],
                [
                    'controller' => ReferentielPromoController::class,
                    'action' => [
                        ReferentielPromoController::ACTION_INDEX,
                        ReferentielPromoController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_PROMO_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ReferentielPromo',
                ],
                [
                    'controller' => ReferentielPromoController::class,
                    'action' => [
                        ReferentielPromoController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_PROMO_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\ReferentielPromo',
                ],
                [
                    'controller' => ReferentielPromoController::class,
                    'action' => [
                        ReferentielPromoController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_PROMO_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\ReferentielPromo',
                ],
                [
                    'controller' => ReferentielPromoController::class,
                    'action' => [
                        ReferentielPromoController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ReferentielPrivilege::REFERENTIEL_PROMO_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ReferentielPromo',
                ],
            ],
        ],
//        Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                ReferentielPromo::RESOURCE_ID => [],
                Source::RESOURCE_ID => [],
            ],
        ],

        'rule_providers' => [
            PrivilegeRuleProvider::class => [
                'allow' => [
                    [
                        'privileges' => [
                            ReferentielPrivilege::REFERENTIEL_PROMO_AFFICHER,
                            ReferentielPrivilege::REFERENTIEL_PROMO_AJOUTER,
                            ReferentielPrivilege::REFERENTIEL_PROMO_MODIFIER,
                            ReferentielPrivilege::REFERENTIEL_PROMO_SUPPRIMER,
                        ],
                        'resources' => [ReferentielPromo::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ReferentielPromo',
                    ],
                    [
                        'privileges' => [
                            ReferentielPrivilege::REFERENTIEL_SOURCE_AFFICHER,
                            ReferentielPrivilege::REFERENTIEL_SOURCE_AJOUTER,
                            ReferentielPrivilege::REFERENTIEL_SOURCE_MODIFIER,
                            ReferentielPrivilege::REFERENTIEL_SOURCE_SUPPRIMER,
                        ],
                        'resources' => [Source::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Source',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'referentiel' => [ //Ajoute les pages liée a UnicaenUtilisateur, UnicaenPriviléges ...
                'type' => Segment::class,
                'options' => [
                    'route' => '/referentiel',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'source' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/source',
                            'defaults' => [
                                'controller' => SourceController::class,
                                'action' => SourceController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => SourceController::class,
                                        'action' => SourceController::ACTION_LISTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => SourceController::class,
                                        'action' => SourceController::ACTION_AJOUTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:source]',
                                    'constraints' => [
                                        'source' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SourceController::class,
                                        'action' => SourceController::ACTION_MODIFIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:source]',
                                    'constraints' => [
                                        'source' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => SourceController::class,
                                        'action' => SourceController::ACTION_SUPPRIMER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'promo' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/promo',
                            'defaults' => [
                                'controller' => ReferentielPromoController::class,
                                'action' => ReferentielPromoController::ACTION_INDEX,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => ReferentielPromoController::class,
                                        'action' => ReferentielPromoController::ACTION_LISTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => ReferentielPromoController::class,
                                        'action' => ReferentielPromoController::ACTION_AJOUTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:referentielPromo]',
                                    'constraints' => [
                                        'referentielPromo' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ReferentielPromoController::class,
                                        'action' => ReferentielPromoController::ACTION_MODIFIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:referentielPromo]',
                                    'constraints' => [
                                        'referentielPromo' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ReferentielPromoController::class,
                                        'action' => ReferentielPromoController::ACTION_SUPPRIMER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'rechercher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/rechercher',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'etudiant' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/etudiant[/:source]',
                                    'defaults' => [
                                        'controller' => ReferentielController::class,
                                        'action' => ReferentielController::ACTION_RECHERCHER_ETUDIANT,
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
            SourceController::class => SourceControllerFactory::class,
            ReferentielPromoController::class => ReferentielPromoControllerFactory::class,
            ReferentielController::class => ReferentielControllerFactory::class,
        ],
    ],

    'form_elements' => [
        'factories' => [
            SourceForm::class => SourceFormFactory::class,
            SourceFieldset::class => SourceFieldsetFactory::class,
            SourceSelectPicker::class => SelectPickerFactory::class,
            CSVImportEtudiantsForm::class => CSVImportEtudiantsFormFactory::class,
            ReferentielImportEtudiantsForm::class => ReferentielImportEtudiantsFormFactory::class,

            ReferentielPromoForm::class => ReferentielPromoFormFactory::class,
            ReferentielPromoFieldset::class => ReferentielPromoFieldsetFactory::class,
            ReferentielPromoSelectPicker::class => SelectPickerFactory::class,
        ],
    ],

    'hydrators' => [
        'factories' => [
            ReferentielPromoHydrator::class => ReferentielPormoHydratorFactory::class,
            CSVImportEtudiantsHydrator::class => ImportEtudiantsHydratorFactory::class,
            ReferentielImportEtudiantsHydrator::class => ImportEtudiantsHydratorFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            MultipleReferentielEtudiantsService::class => MultipleReferentielsEtudiantsServiceFactory::class,
            SourceService::class => SourceServiceFactory::class,
            ReferentielPromoService::class => ReferentielPromoServiceFactory::class,
            CSVEtudiantService::class => CSVEtudiantServiceFactory::class,
            ReferentielDbImportEtudiantService::class => ReferentielDbImportEtudiantServiceFactory::class,

            LdapEtudiantService::class => LdapEtudiantServiceFactory::class,
            LocalEtudiantService::class => RechercheEtudiantLocalServiceFactory::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'source' => SourceViewHelper::class,
            'referentielPromo' => ReferentielPromoViewHelper::class,
        ],
        'invokables' => [
            'source' => SourceViewHelper::class,
            'referentielPromo' => ReferentielPromoViewHelper::class,
        ],
    ],
];


