<?php
//Gestions des terrains de stages
use Application\Controller\Terrain\CategorieStageController;
use Application\Controller\Terrain\Factory\CategorieStageControllerFactory;
use Application\Controller\Terrain\Factory\TerrainStageControllerFactory;
use Application\Controller\Terrain\TerrainStageController;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Form\TerrainStage\CategorieStageForm;
use Application\Form\TerrainStage\Element\CategorieStagePrincipauxSelectPicker;
use Application\Form\TerrainStage\Element\CategorieStageSecondaireSelectPicker;
use Application\Form\TerrainStage\Element\CategorieStageSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStagePrincipalSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSecondaireSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Application\Form\TerrainStage\Factory\CategorieStageFieldsetFactory;
use Application\Form\TerrainStage\Factory\CategorieStageFormFactory;
use Application\Form\TerrainStage\Factory\TerrainStageFieldsetFactory;
use Application\Form\TerrainStage\Factory\TerrainStageFormFactory;
use Application\Form\TerrainStage\Factory\TerrainStageHydratorFactory;
use Application\Form\TerrainStage\Fieldset\CategorieStageFieldset;
use Application\Form\TerrainStage\Fieldset\TerrainStageFieldset;
use Application\Form\TerrainStage\Hydrator\TerrainStageHydrator;
use Application\Form\TerrainStage\TerrainStageForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\TerrainPrivileges;
use Application\Service\TerrainStage\CategorieStageService;
use Application\Service\TerrainStage\Factory\CategorieStageServiceFactory;
use Application\Service\TerrainStage\Factory\TerrainStageServiceFactory;
use Application\Service\TerrainStage\TerrainStageService;
use Application\Validator\Import\CategorieStageCsvImportValidator;
use Application\Validator\Import\Factory\AbstractImportCsvValidatorFactory;
use Application\Validator\Import\TerrainStageCsvImportValidator;
use Application\View\Helper\Terrains\CategorieStageViewHelper;
use Application\View\Helper\Terrains\Factory\TerrainStageViewHelperFactory;
use Application\View\Helper\Terrains\TerrainStageViewHelper;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                /** Terrains de stages */
                [
                    'controller' => TerrainStageController::class,
                    'action' => [
                        TerrainStageController::ACTION_INDEX,
                        TerrainStageController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        TerrainPrivileges::TERRAIN_STAGE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\TerrainStage',
                ],
                [
                    'controller' => TerrainStageController::class,
                    'action' => [
                        TerrainStageController::ACTION_AFFICHER,
                        TerrainStageController::ACTION_LISTER_CONTACTS,
                        TerrainStageController::ACTION_AFFICHER_MODELE_CONVENTION,
                    ],
                    'privileges' => [
                        TerrainPrivileges::TERRAIN_STAGE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\TerrainStage',
                ],
                [
                    'controller' => TerrainStageController::class,
                    'action' => [TerrainStageController::ACTION_AJOUTER],
                    'privileges' => [
                        TerrainPrivileges::TERRAIN_STAGE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\TerrainStage',
                ],
                [
                    'controller' => TerrainStageController::class,
                    'action' => [
                        TerrainStageController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        TerrainPrivileges::TERRAIN_STAGE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\TerrainStage',
                ],
                [
                    'controller' => TerrainStageController::class,
                    'action' => [
                        TerrainStageController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        TerrainPrivileges::TERRAIN_STAGE_SUPPRIMER,
                    ],
                ],
                [
                    'controller' => TerrainStageController::class,
                    'action' => [
                        TerrainStageController::ACTION_IMPORTER,
                    ],
                    'privileges' => [
                        TerrainPrivileges::TERRAINS_IMPORTER,
                    ],
                    'assertion' => 'Assertion\\TerrainStage',
                ],


                /** Catégories des stages */
                [
                    'controller' => CategorieStageController::class,
                    'action' => [
                        CategorieStageController::ACTION_INDEX,
                        CategorieStageController::ACTION_LISTER,
                        CategorieStageController::ACTION_AFFICHER,
                    ],
                    'privileges' => [
                        TerrainPrivileges::CATEGORIE_STAGE_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\CategorieStage',
                ],
                [
                    'controller' => CategorieStageController::class,
                    'action' => [CategorieStageController::ACTION_AJOUTER],
                    'privileges' => [
                        TerrainPrivileges::CATEGORIE_STAGE_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\CategorieStage',
                ],
                [
                    'controller' => CategorieStageController::class,
                    'action' => [CategorieStageController::ACTION_MODIFIER],
                    'privileges' => [
                        TerrainPrivileges::CATEGORIE_STAGE_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\CategorieStage',
                ],
                [
                    'controller' => CategorieStageController::class,
                    'action' => [CategorieStageController::ACTION_SUPPRIMER],
                    'privileges' => [
                        TerrainPrivileges::CATEGORIE_STAGE_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\CategorieStage',
                ],
                [
                    'controller' => CategorieStageController::class,
                    'action' => [
                        CategorieStageController::ACTION_IMPORTER,
                    ],
                    'privileges' => [
                        TerrainPrivileges::TERRAINS_IMPORTER,
                    ],
                    'assertion' => 'Assertion\\CategorieStage',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                TerrainStage::RESOURCE_ID => [],
                CategorieStage::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            TerrainPrivileges::TERRAIN_STAGE_AFFICHER,
                            TerrainPrivileges::TERRAIN_STAGE_AJOUTER,
                            TerrainPrivileges::TERRAIN_STAGE_MODIFIER,
                            TerrainPrivileges::TERRAIN_STAGE_SUPPRIMER,
                        ],
                        'resources' => [TerrainStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\TerrainStage',
                    ],
                    [
                        'privileges' => [
                            TerrainPrivileges::CATEGORIE_STAGE_AFFICHER,
                            TerrainPrivileges::CATEGORIE_STAGE_AJOUTER,
                            TerrainPrivileges::CATEGORIE_STAGE_MODIFIER,
                            TerrainPrivileges::CATEGORIE_STAGE_SUPPRIMER,
                        ],
                        'resources' => [CategorieStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\CategorieStage',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'terrain' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/terrain',
                    'defaults' => [
                        'controller' => TerrainStageController::class,
                        'action' => TerrainStageController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:terrainStage]',
                            'constraints' => [
                                'terrainStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'lister' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:terrainStage]',
                            'constraints' => [
                                'terrainStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:terrainStage]',
                            'constraints' => [
                                'terrainStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'importer' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/importer',
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_IMPORTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'contacts' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/contacts',
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_LISTER_CONTACTS,
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'lister' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/lister[/:terrainStage]',
                                    'constraints' => [
                                        'terrainStage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => TerrainStageController::class,
                                        'action' => TerrainStageController::ACTION_LISTER_CONTACTS,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'modele-convention' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/modele-convention',
                            'defaults' => [
                                'controller' => TerrainStageController::class,
                                'action' => TerrainStageController::ACTION_AFFICHER_MODELE_CONVENTION,
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'afficher' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/afficher[/:terrainStage]',
                                    'constraints' => [
                                        'terrainStage' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => TerrainStageController::class,
                                        'action' => TerrainStageController::ACTION_AFFICHER_MODELE_CONVENTION,
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'categorie-stage' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/categorie-stage',
                    'defaults' => [
                        'controller' => CategorieStageController::class,
                        'action' => CategorieStageController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'lister' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => CategorieStageController::class,
                                'action' => CategorieStageController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:categorieStage]',
                            'constraints' => [
                                'categorieStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => CategorieStageController::class,
                                'action' => CategorieStageController::ACTION_AFFICHER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'ajouter' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => CategorieStageController::class,
                                'action' => CategorieStageController::ACTION_AJOUTER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:categorieStage]',
                            'constraints' => [
                                'categorieStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => CategorieStageController::class,
                                'action' => CategorieStageController::ACTION_MODIFIER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:categorieStage]',
                            'constraints' => [
                                'categorieStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => CategorieStageController::class,
                                'action' => CategorieStageController::ACTION_SUPPRIMER,
                            ],
                        ],
                        'may_terminate' => true,
                    ],
                    'importer' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/importer',
                            'defaults' => [
                                'controller' => CategorieStageController::class,
                                'action' => CategorieStageController::ACTION_IMPORTER,
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
            TerrainStageController::class => TerrainStageControllerFactory::class,
            CategorieStageController::class => CategorieStageControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            //Elements
            CategorieStageSelectPicker::class => SelectPickerFactory::class,
            CategorieStagePrincipauxSelectPicker::class => SelectPickerFactory::class,
            CategorieStageSecondaireSelectPicker::class => SelectPickerFactory::class,
            TerrainStageSelectpicker::class => SelectPickerFactory::class,
            TerrainStagePrincipalSelectPicker::class => SelectPickerFactory::class,
            TerrainStageSecondaireSelectPicker::class => SelectPickerFactory::class,

            TerrainStageForm::class => TerrainStageFormFactory::class,
            CategorieStageForm::class => CategorieStageFormFactory::class,
            // Fieldset
            TerrainStageFieldset::class => TerrainStageFieldsetFactory::class,
            CategorieStageFieldset::class => CategorieStageFieldsetFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            CategorieStageService::class => CategorieStageServiceFactory::class,
            TerrainStageService::class => TerrainStageServiceFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            TerrainStageHydrator::class => TerrainStageHydratorFactory::class,
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'terrainStage' => TerrainStageViewHelper::class,
            'categorieStage' => CategorieStageViewHelper::class,
        ],
        'invokables' => [
            'categorieStage' => CategorieStageViewHelper::class,
        ],
        'factories' => [
            TerrainStageViewHelper::class => TerrainStageViewHelperFactory::class,
        ],
    ],

    'validators' => [
        'factories' => [
            TerrainStageCsvImportValidator::class => AbstractImportCsvValidatorFactory::class,
            CategorieStageCsvImportValidator::class => AbstractImportCsvValidatorFactory::class,
        ],
    ],
    'session_containers' => [
    ],

];
