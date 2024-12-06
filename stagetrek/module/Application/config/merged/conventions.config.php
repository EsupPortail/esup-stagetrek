<?php
//Gestions des stages
use Application\Controller\Convention\ConventionStageController;
use Application\Controller\Convention\Factory\ConventionStageControllerFactory;
use Application\Controller\Convention\Factory\ModeleConventionControllerFactory;
use Application\Controller\Convention\ModeleConventionController;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\Stage;
use Application\Form\Convention\ConventionStageTeleversementForm;
use Application\Form\Convention\Element\ModeleConventionStageSelectPicker;
use Application\Form\Convention\Factory\ConventionStageTeleversementFieldsetFactory;
use Application\Form\Convention\Factory\ConventionStageTeleversementFormFactory;
use Application\Form\Convention\Factory\ModeleConventionStageFieldsetFactory;
use Application\Form\Convention\Factory\ModeleConventionStageFormFactory;
use Application\Form\Convention\Factory\ModeleConventionStageHydratorFactory;
use Application\Form\Convention\Fieldset\ConventionStageTeleversementFieldset;
use Application\Form\Convention\Fieldset\ModeleConventionStageFieldset;
use Application\Form\Convention\Hydrator\ModeleConventionStageHydrator;
use Application\Form\Convention\ModeleConventionStageForm;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ConventionsPrivileges;
use Application\Service\ConventionStage\ConventionStageFileNameFormatter;
use Application\Service\ConventionStage\ConventionStageService;
use Application\Service\ConventionStage\Factory\ConventionStageServiceFactory;
use Application\Service\ConventionStage\Factory\ModeleConventionStageServiceFactory;
use Application\Service\ConventionStage\ModeleConventionStageService;
use Application\View\Helper\Convention\ConventionViewHelper;
use Application\View\Helper\Convention\Factory\ConventionViewHelperFactory;
use Application\View\Helper\Convention\ModeleConventionViewHelper;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => ConventionStageController::class,
                    'action' => [
                        ConventionStageController::ACTION_AFFICHER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::CONVENTION_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ConventionStage',
                ],
                [ //TODO : remplacer le créer par un uploader
                    'controller' => ConventionStageController::class,
                    'action' => [
                        ConventionStageController::ACTION_TELEVERSER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::CONVENTION_TELEVERSER,
                    ],
                    'assertion' => 'Assertion\\ConventionStage',
                ],
                [
                    'controller' => ConventionStageController::class,
                    'action' => [
                        ConventionStageController::ACTION_GENERER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::CONVENTION_GENERER,
                    ],
                    'assertion' => 'Assertion\\ConventionStage',
                ],
                [
                    'controller' => ConventionStageController::class,
                    'action' => [
                        ConventionStageController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::CONVENTION_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ConventionStage',
                ],
                [
                    'controller' => ConventionStageController::class,
                    'action' => [
                        ConventionStageController::ACTION_TELECHARGER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::CONVENTION_TELECHARGER,
                    ],
                    'assertion' => 'Assertion\\ConventionStage',
                ],
                [
                    'controller' => ModeleConventionController::class,
                    'action' => [
                        ModeleConventionController::ACTION_INDEX,
                        ModeleConventionController::ACTION_LISTER,
                        ModeleConventionController::ACTION_AFFICHER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::MODELE_CONVENTION_AFFICHER,
                    ],
                    'assertion' => 'Assertion\\ModeleConventionStage',
                ],
                [
                    'controller' => ModeleConventionController::class,
                    'action' => [
                        ModeleConventionController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::MODELE_CONVENTION_AJOUTER,
                    ],
                    'assertion' => 'Assertion\\ModeleConventionStage',
                ],
                [
                    'controller' => ModeleConventionController::class,
                    'action' => [
                        ModeleConventionController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::MODELE_CONVENTION_MODIFIER,
                    ],
                    'assertion' => 'Assertion\\ModeleConventionStage',
                ],
                [
                    'controller' => ModeleConventionController::class,
                    'action' => [
                        ModeleConventionController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        ConventionsPrivileges::MODELE_CONVENTION_SUPPRIMER,
                    ],
                    'assertion' => 'Assertion\\ModeleConventionStage',
                ],
            ],
        ],
        //Definition des ressources utilisées pour les assertions
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Stage::RESOURCE_ID => [],
                ModeleConventionStage::RESOURCE_ID => [],
                ArrayRessource::RESOURCE_ID => [],
            ],
        ],
        //Configurations des assertions sur les entités (implique de surcharger derriére la fonction assertEntity
        'rule_providers' => [
            'UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider' => [
                'allow' => [
                    [
                        'privileges' => [
                            ConventionsPrivileges::CONVENTION_AFFICHER,
                            ConventionsPrivileges::CONVENTION_TELEVERSER,
                            ConventionsPrivileges::CONVENTION_GENERER,
                            ConventionsPrivileges::CONVENTION_MODIFIER,
                            ConventionsPrivileges::CONVENTION_SUPPRIMER,
                            ConventionsPrivileges::CONVENTION_TELECHARGER,
                        ],
                        'resources' => [Stage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ConventionStage',
                    ],
                    [
                        'privileges' => [
                            ConventionsPrivileges::MODELE_CONVENTION_AFFICHER,
                            ConventionsPrivileges::MODELE_CONVENTION_AJOUTER,
                            ConventionsPrivileges::MODELE_CONVENTION_MODIFIER,
                            ConventionsPrivileges::MODELE_CONVENTION_SUPPRIMER,
                        ],
                        'resources' => [ModeleConventionStage::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\ModeleConventionStage',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'convention' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/convention',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ConventionStageController::class,
                                'action' => ConventionStageController::ACTION_AFFICHER,
                            ],
                        ],
                    ],
                    'televerser' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/televerser[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ConventionStageController::class,
                                'action' => ConventionStageController::ACTION_TELEVERSER,
                            ],
                        ],
                    ],
                    'generer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/generer[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ConventionStageController::class,
                                'action' => ConventionStageController::ACTION_GENERER,
                            ],
                        ],
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ConventionStageController::class,
                                'action' => ConventionStageController::ACTION_SUPPRIMER,
                            ],
                        ],
                    ],
                    'telecharger' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/telecharger[/:stage]',
                            'constraints' => [
                                'stage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ConventionStageController::class,
                                'action' => ConventionStageController::ACTION_TELECHARGER,
                            ],
                        ],
                    ],
                ],
            ],
            'modeles-conventions' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/modeles-conventions',
                    'defaults' => [
                        'controller' => ModeleConventionController::class,
                        'action' => ModeleConventionController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'lister' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => ModeleConventionController::class,
                                'action' => ModeleConventionController::ACTION_LISTER,
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                        ],
                    ],
                ],
            ],
            'modele-convention' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/modele-convention',
                    'defaults' => [
                        'controller' => ModeleConventionController::class,
                        'action' => ModeleConventionController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'afficher' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/afficher[/:modeleConventionStage]',
                            'constraints' => [
                                'modeleConventionStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ModeleConventionController::class,
                                'action' => ModeleConventionController::ACTION_AFFICHER,
                            ],
                        ],
                    ],
                    'ajouter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => ModeleConventionController::class,
                                'action' => ModeleConventionController::ACTION_AJOUTER,
                            ],
                        ],
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:modeleConventionStage]',
                            'constraints' => [
                                'modeleConventionStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ModeleConventionController::class,
                                'action' => ModeleConventionController::ACTION_MODIFIER,
                            ],
                        ],
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:modeleConventionStage]',
                            'constraints' => [
                                'modeleConventionStage' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => ModeleConventionController::class,
                                'action' => ModeleConventionController::ACTION_SUPPRIMER,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            ConventionStageController::class => ConventionStageControllerFactory::class,
            ModeleConventionController::class => ModeleConventionControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            ConventionStageTeleversementForm::class => ConventionStageTeleversementFormFactory::class,
            ConventionStageTeleversementFieldset::class => ConventionStageTeleversementFieldsetFactory::class,

            ModeleConventionStageForm::class => ModeleConventionStageFormFactory::class,
            ModeleConventionStageFieldset::class => ModeleConventionStageFieldsetFactory::class,
            ModeleConventionStageSelectPicker::class => SelectPickerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            ConventionStageService::class => ConventionStageServiceFactory::class,
            ModeleConventionStageService::class => ModeleConventionStageServiceFactory::class,
        ],
        'invokables' => [
            ConventionStageFileNameFormatter::class => ConventionStageFileNameFormatter::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            ModeleConventionStageHydrator::class => ModeleConventionStageHydratorFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'convention' => ConventionViewHelper::class,
            'modeleConvention' => ModeleConventionViewHelper::class,
        ],
        'invokables' => [
            'modeleConvention' => ModeleConventionViewHelper::class,
        ],
        'factories' => [
            ConventionViewHelper::class => ConventionViewHelperFactory::class
        ]
    ],
    'session_containers' => [
    ],

];
