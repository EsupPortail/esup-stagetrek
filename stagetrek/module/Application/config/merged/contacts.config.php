<?php
//Gestions des contacts de stages
use Application\Controller\Contact\ContactController;
use Application\Controller\Contact\ContactStageController;
use Application\Controller\Contact\ContactTerrainController;
use Application\Controller\Contact\Factory\ContactControllerFactory;
use Application\Controller\Contact\Factory\ContactStageControllerFactory;
use Application\Controller\Contact\Factory\ContactTerrainControllerFactory;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Contacts\ContactForm;
use Application\Form\Contacts\ContactRechercheForm;
use Application\Form\Contacts\ContactStageForm;
use Application\Form\Contacts\ContactTerrainForm;
use Application\Form\Contacts\Element\ContactSelectPicker;
use Application\Form\Contacts\Factory\ContactFieldsetFactory;
use Application\Form\Contacts\Factory\ContactFormFactory;
use Application\Form\Contacts\Factory\ContactHydratorFactory;
use Application\Form\Contacts\Factory\ContactRechercheFormFactory;
use Application\Form\Contacts\Factory\ContactStageFieldsetFactory;
use Application\Form\Contacts\Factory\ContactStageFormFactory;
use Application\Form\Contacts\Factory\ContactStageHydratorFactory;
use Application\Form\Contacts\Factory\ContactStageValidatorFactory;
use Application\Form\Contacts\Factory\ContactTerrainFieldsetFactory;
use Application\Form\Contacts\Factory\ContactTerrainFormFactory;
use Application\Form\Contacts\Factory\ContactTerrainHydratorFactory;
use Application\Form\Contacts\Fieldset\ContactFieldset;
use Application\Form\Contacts\Fieldset\ContactStageFieldset;
use Application\Form\Contacts\Fieldset\ContactTerrainFieldset;
use Application\Form\Contacts\Hydrator\ContactHydrator;
use Application\Form\Contacts\Hydrator\ContactStageHydrator;
use Application\Form\Contacts\Hydrator\ContactTerrainHydrator;
use Application\Form\Contacts\Validator\ContactStageValidator;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ContactPrivileges;
use Application\Service\Contact\ContactService;
use Application\Service\Contact\ContactStageService;
use Application\Service\Contact\ContactTerrainService;
use Application\Service\Contact\Factory\ContactServiceFactory;
use Application\Service\Contact\Factory\ContactStageServiceFactory;
use Application\Service\Contact\Factory\ContactTerrainServiceFactory;
use Application\Validator\Import\ContactCsvImportValidator;
use Application\Validator\Import\ContactTerrainCsvImportValidator;
use Application\Validator\Import\Factory\AbstractImportCsvValidatorFactory;
use Application\View\Helper\Contacts\ContactStageViewHelper;
use Application\View\Helper\Contacts\ContactTerrainViewHelper;
use Application\View\Helper\Contacts\ContactViewHelper;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                /** Contacts */
                [
                    'controller' => ContactController::class,
                    'action' => [
                        ContactController::ACTION_INDEX,
                        ContactController::ACTION_LISTER,
                        ContactController::ACTION_AFFICHER,
                        ContactController::ACTION_AFFICHER_INFOS,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\Contact',
                ],
                [
                    'controller' => ContactController::class,
                    'action' => [
                        ContactController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\Contact',
                ],
                [
                    'controller' => ContactController::class,
                    'action' => [
                        ContactController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\Contact',
                ],
                [
                    'controller' => ContactController::class,
                    'action' => [
                        ContactController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\Contact',
                ],
                [
                    'controller' => ContactController::class,
                    'action' => [
                        ContactController::ACTION_IMPORTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_IMPORTER,
                    ],
                'assertion' => 'Assertion\\Contact',
                ],
//                [
//                    'controller' => ContactController::class,
//                    'action' => [
//                        ContactController::ACTION_EXPORTER,
//                    ],
//                    'privileges' => [
//                        ContactPrivileges::CONTACT_EXPORTER,
//                    ],
//                'assertion' => 'Assertion\\Contact',
//                ],

                /** Contacts des stages */
                [
                    'controller' => ContactStageController::class,
                    'action' => [
                        ContactStageController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_STAGE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ContactStage',
                ],
                [
                    'controller' => ContactStageController::class,
                    'action' => [
                        ContactStageController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_STAGE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\ContactStage',
                ],
                [
                    'controller' => ContactStageController::class,
                    'action' => [
                        ContactStageController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_STAGE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\ContactStage',
                ],
                [
                    'controller' => ContactStageController::class,
                    'action' => [
                        ContactStageController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_STAGE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ContactStage',
                ],
                [
                    'controller' => ContactStageController::class,
                    'action' => [
                        ContactStageController::ACTION_SEND_MAIL_VALIDATION,
                    ],
                    'privileges' => [
                        ContactPrivileges::SEND_MAIL_VALIDATION,
                    ],
                    'assertion' => 'Assertion\\ContactStage',
                ],

                /** Contacts des terrains */
                [
                    'controller' => ContactTerrainController::class,
                    'action' => [
                        ContactTerrainController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_TERRAIN_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ContactTerrain',
                ],
                [
                    'controller' => ContactTerrainController::class,
                    'action' => [
                        ContactTerrainController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_TERRAIN_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\ContactTerrain',
                ],
                [
                    'controller' => ContactTerrainController::class,
                    'action' => [
                        ContactTerrainController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_TERRAIN_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\ContactTerrain',
                ],
                [
                    'controller' => ContactTerrainController::class,
                    'action' => [
                        ContactTerrainController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_TERRAIN_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ContactTerrain',
                ],
                [
                    'controller' => ContactTerrainController::class,
                    'action' => [
                        ContactTerrainController::ACTION_IMPORTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_TERRAIN_IMPORTER,
                    ],
                    'assertion' => 'Assertion\\ContactTerrain',
                ],
//                TODO : a revoir pour l'import
                [
                    'controller' => ContactTerrainController::class,
                    'action' => [
                        ContactTerrainController::ACTION_EXPORTER,
                    ],
                    'privileges' => [
                        ContactPrivileges::CONTACT_TERRAIN_EXPORTER,
                    ],
                    'assertion' => 'Assertion\\ContactTerrain',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Contact::RESOURCE_ID => [],
                ContactStage::RESOURCE_ID => [],
                ContactTerrain::RESOURCE_ID => [],
                Stage::RESOURCE_ID => [],
                TerrainStage::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            ContactPrivileges::CONTACT_AFFICHER,
                            ContactPrivileges::CONTACT_AJOUTER,
                            ContactPrivileges::CONTACT_MODIFIER,
                            ContactPrivileges::CONTACT_SUPPRIMER,
                            ContactPrivileges::CONTACT_EXPORTER,
                            ContactPrivileges::CONTACT_IMPORTER,
                        ],
                        'resources' => [Contact::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\Contact',
                    ],
                    [
                        'privileges' => [
                            ContactPrivileges::CONTACT_STAGE_AFFICHER,
                            ContactPrivileges::CONTACT_STAGE_AJOUTER,
                            ContactPrivileges::CONTACT_STAGE_MODIFIER,
                            ContactPrivileges::CONTACT_STAGE_SUPPRIMER,
                            ContactPrivileges::SEND_MAIL_VALIDATION,
                        ],
                        'resources' => [Contact::RESOURCE_ID, Stage::RESOURCE_ID, ContactStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ContactStage',
                    ],
                    [
                        'privileges' => [
                            ContactPrivileges::CONTACT_TERRAIN_AFFICHER,
                            ContactPrivileges::CONTACT_TERRAIN_AJOUTER,
                            ContactPrivileges::CONTACT_TERRAIN_MODIFIER,
                            ContactPrivileges::CONTACT_TERRAIN_SUPPRIMER,
                            ContactPrivileges::CONTACT_TERRAIN_EXPORTER,
                            ContactPrivileges::CONTACT_TERRAIN_IMPORTER,
                        ],
                        'resources' =>  [Contact::RESOURCE_ID, TerrainStage::RESOURCE_ID, ContactTerrain::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ContactTerrain',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'contact' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/contact',
                    'defaults' => [
                        'controller' => ContactController::class,
                        'action' => ContactController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:contact]',
                            'constraints' => [
                                'contact' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'lister' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'infos' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/infos[/:contact]',
                            'constraints' => [
                                'contact' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_AFFICHER_INFOS,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:contact]',
                            'constraints' => [
                                'contact' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:contact]',
                            'constraints' => [
                                'contact' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'importer' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/importer',
                            'defaults' => [
                                'controller' => ContactController::class,
                                'action' => ContactController::ACTION_IMPORTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
//                    'exporter' => [
//                        'type' => Literal::class,
//                        'options' => [
//                            'route' => '/exporter',
//                            'defaults' => [
//                                'controller' => ContactController::class,
//                                'action' => ContactController::ACTION_EXPORTER,
//                            ],
//                        ],
//                        'may_terminate' => true,
//                    ],
                    'terrain' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/terrain',
                            'defaults' => [
                                'controller' => ContactTerrainController::class,
                                'action' => ContactTerrainController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister[/:contact]',
                                    'constraints' => [
                                        'contact' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactTerrainController::class,
                                        'action' => ContactTerrainController::ACTION_LISTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter[/:contact[/:terrainStage]]',
                                    'constraints' => [
                                        'contact' => '[0-9]+',
                                        'terrainStage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactTerrainController::class,
                                        'action' => ContactTerrainController::ACTION_AJOUTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:contactTerrain]',
                                    'constraints' => [
                                        'contactTerrain' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactTerrainController::class,
                                        'action' => ContactTerrainController::ACTION_MODIFIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:contactTerrain]',
                                    'constraints' => [
                                        'contactTerrain' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactTerrainController::class,
                                        'action' => ContactTerrainController::ACTION_SUPPRIMER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'importer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/importer',
                                    'defaults' => [
                                        'controller' => ContactTerrainController::class,
                                        'action' => ContactTerrainController::ACTION_IMPORTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'stage' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/stage',
                            'defaults' => [
                                'controller' => ContactStageController::class,
                                'action' => ContactStageController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister[/:contact]',
                                    'constraints' => [
                                        'contact' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactStageController::class,
                                        'action' => ContactStageController::ACTION_LISTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/ajouter[/:contact[/:stage]]',
                                    'constraints' => [
                                        'contact' => '[0-9]+',
                                        'stage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactStageController::class,
                                        'action' => ContactStageController::ACTION_AJOUTER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:contactStage]',
                                    'constraints' => [
                                        'contactStage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactStageController::class,
                                        'action' => ContactStageController::ACTION_MODIFIER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:contactStage]',
                                    'constraints' => [
                                        'contactStage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => ContactStageController::class,
                                        'action' => ContactStageController::ACTION_SUPPRIMER,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'mail-validation' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/mail-validation',
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'envoyer' => [
                                        'type' => Segment::class,
                                        'options' => [
                                            'route' => '/envoyer[/:contactStage]',
                                            'constraints' => [
                                                'contactStage' => '[0-9]+',
                                            ],
                                            'defaults' => [
                                                'controller' => ContactStageController::class,
                                                'action' => ContactStageController::ACTION_SEND_MAIL_VALIDATION,
                                            ],
                                        ],
                                        'may_terminate' => true,
                                    ],
                                ]
                            ],
                        ],
                    ],

                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            ContactController::class => ContactControllerFactory::class,
            ContactStageController::class => ContactStageControllerFactory::class,
            ContactTerrainController::class => ContactTerrainControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            ContactForm::class => ContactFormFactory::class,
            ContactFieldset::class => ContactFieldsetFactory::class,
            ContactSelectPicker::class => SelectPickerFactory::class,
            ContactStageForm::class => ContactStageFormFactory::class,
            ContactStageFieldset::class => ContactStageFieldsetFactory::class,
            ContactTerrainForm::class => ContactTerrainFormFactory::class,
            ContactTerrainFieldset::class => ContactTerrainFieldsetFactory::class,
            ContactRechercheForm::class => ContactRechercheFormFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            ContactService::class => ContactServiceFactory::class,
            ContactStageService::class => ContactStageServiceFactory::class,
            ContactTerrainService::class => ContactTerrainServiceFactory::class,],
    ],
    'hydrators' => [
        'factories' => [
            ContactHydrator::class => ContactHydratorFactory::class,
            ContactStageHydrator::class => ContactStageHydratorFactory::class,
            ContactTerrainHydrator::class => ContactTerrainHydratorFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'contact' => ContactViewHelper::class,
            'contactTerrain' => ContactTerrainViewHelper::class,
            'contactStage' => ContactStageViewHelper::class,
        ],
        'invokables' => [
            'contact' => ContactViewHelper::class,
            'contactTerrain' => ContactTerrainViewHelper::class,
            'contactStage' => ContactStageViewHelper::class,
        ],
        'factories' => [
        ]
    ],

    'validators' => [
        'factories' => [
            ContactStageValidator::class => ContactStageValidatorFactory::class,
            ContactCsvImportValidator::class => AbstractImportCsvValidatorFactory::class,
            ContactTerrainCsvImportValidator::class => AbstractImportCsvValidatorFactory::class,
        ],
    ],
    'session_containers' => [
    ],
];
