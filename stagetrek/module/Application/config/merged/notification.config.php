<?php

use Application\Controller\Notification\Factory\FaqCategorieControllerFactory;
use Application\Controller\Notification\Factory\FaqQuestionControllerFactory;
use Application\Controller\Notification\Factory\MessageInfoControllerFactory;
use Application\Controller\Notification\FaqCategorieController;
use Application\Controller\Notification\FaqQuestionController;
use Application\Controller\Notification\MessageInfoController;
use Application\Entity\Db\Faq;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Entity\Db\MessageInfo;
use Application\Form\Notification\Factory\FaqCategorieQuestionFieldsetFactory;
use Application\Form\Notification\Factory\FaqCategorieQuestionFormFactory;
use Application\Form\Notification\Factory\FaqQuestionFieldsetFactory;
use Application\Form\Notification\Factory\FaqQuestionFormFactory;
use Application\Form\Notification\Factory\FaqQuestionHydratorFactory;
use Application\Form\Notification\Factory\MessageInfoFieldsetFactory;
use Application\Form\Notification\Factory\MessageInfoFormFactory;
use Application\Form\Notification\Factory\MessageInfoHydratorFactory;
use Application\Form\Notification\FaqCategorieQuestionForm;
use Application\Form\Notification\FaqQuestionForm;
use Application\Form\Notification\Fieldset\FaqCategorieQuestionFieldset;
use Application\Form\Notification\Fieldset\FaqQuestionFieldset;
use Application\Form\Notification\Fieldset\MessageInfoFieldset;
use Application\Form\Notification\Hydrator\FaqQuestionHydrator;
use Application\Form\Notification\Hydrator\MessageInfoHydrator;
use Application\Form\Notification\MessageInfoForm;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\FaqPrivileges;
use Application\Provider\Privilege\MessagePrivilege;
use Application\Service\Notification\Factory\FaqCategorieQuestionServiceFactory;
use Application\Service\Notification\Factory\FaqServiceFactory;
use Application\Service\Notification\Factory\MessageInfoServiceFactory;
use Application\Service\Notification\FaqCategorieQuestionService;
use Application\Service\Notification\FaqService;
use Application\Service\Notification\MessageInfoService;
use Application\View\Helper\Notification\Factory\MessageInfoViewHelperFactory;
use Application\View\Helper\Notification\FAQCategorieQuestionViewHelper;
use Application\View\Helper\Notification\FAQViewHelper;
use Application\View\Helper\Notification\MessageInfoViewHelper;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use UnicaenPrivilege\Guard\PrivilegeController;
use UnicaenPrivilege\Provider\Rule\PrivilegeRuleProvider;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => FaqQuestionController::class,
                    'action' => [
                        FaqQuestionController::ACTION_INDEX,
                        FaqQuestionController::ACTION_LISTER,
                    ],
                    'roles' => [],
                    'assertion' => 'Assertion\\FaqQuestion',
                ],
                [
                    'controller' => FaqQuestionController::class,
                    'action' => [
                        FaqQuestionController::ACTION_AJOUTER,
                    ],
                    'privileges' => [FaqPrivileges::FAQ_QUESTION_AJOUTER],
                    'assertion' => 'Assertion\\FaqQuestion',
                ],
                [
                    'controller' => FaqQuestionController::class,
                    'action' => [
                        FaqQuestionController::ACTION_MODIFIER,
                    ],
                    'privileges' => [FaqPrivileges::FAQ_QUESTION_MODIFIER],
                    'assertion' => 'Assertion\\FaqQuestion',
                ],
                [
                    'controller' => FaqQuestionController::class,
                    'action' => [
                        FaqQuestionController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [FaqPrivileges::FAQ_QUESTION_SUPPRIMER],
                    'assertion' => 'Assertion\\FaqQuestion',
                ],
                [
                    'controller' => FaqCategorieController::class,
                    'action' => [
                        FaqCategorieController::ACTION_INDEX,
                        FaqCategorieController::ACTION_LISTER,
                    ],
                    'privileges' => [FaqPrivileges::FAQ_CATEGORIE_AFFICHER],
                    'assertion' => 'Assertion\\FaqCategorie',
//                    'roles' => ['guest']
                ],
                [
                    'controller' => FaqCategorieController::class,
                    'action' => [
                        FaqCategorieController::ACTION_AJOUTER
                    ],
                    'privileges' => [FaqPrivileges::FAQ_CATEGORIE_AJOUTER],
                    'assertion' => 'Assertion\\FaqCategorie',
                ],
                [
                    'controller' => FaqCategorieController::class,
                    'action' => [
                        FaqCategorieController::ACTION_MODIFIER,
                    ],
                    'privileges' => [FaqPrivileges::FAQ_CATEGORIE_MODIFIER],
                    'assertion' => 'Assertion\\FaqCategorie',
                ],
                [
                    'controller' => FaqCategorieController::class,
                    'action' => [
                        FaqCategorieController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [FaqPrivileges::FAQ_CATEGORIE_SUPPRIMER],
                    'assertion' => 'Assertion\\FaqCategorie',
                ],

                [
                    'controller' => MessageInfoController::class,
                    'action' => [
                        MessageInfoController::ACTION_INDEX,
                        MessageInfoController::ACTION_LISTER,
                    ],
                    'privileges' => [
                        MessagePrivilege::MESSAGE_INFO_AFFICHER
                    ],
                    'assertion' => 'Assertion\\MessageInfo',
                ],
                [
                    'controller' => MessageInfoController::class,
                    'action' => [
                        MessageInfoController::ACTION_AJOUTER,
                    ],
                    'privileges' => [
                        MessagePrivilege::MESSAGE_INFO_AJOUTER
                    ],
                    'assertion' => 'Assertion\\MessageInfo',
                ],
                [
                    'controller' => MessageInfoController::class,
                    'action' => [
                        MessageInfoController::ACTION_MODIFIER,
                    ],
                    'privileges' => [
                        MessagePrivilege::MESSAGE_INFO_MODIFIER
                    ],
                    'assertion' => 'Assertion\\MessageInfo',
                ],
                [
                    'controller' => MessageInfoController::class,
                    'action' => [
                        MessageInfoController::ACTION_SUPPRIMER,
                    ],
                    'privileges' => [
                        MessagePrivilege::MESSAGE_INFO_SUPPRIMER
                    ],
                    'assertion' => 'Assertion\\MessageInfo',
                ],
            ],
        ],

        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                Faq::RESOURCE_ID => [],
                FaqCategorieQuestion::RESOURCE_ID => [],
                MessageInfo::RESOURCE_ID => [],
            ],
        ],

        'rule_providers' => [
            PrivilegeRuleProvider::class => [
                'allow' => [
                    [
                        'privileges' => [
                            FaqPrivileges::FAQ_QUESTION_AFFICHER,
                            FaqPrivileges::FAQ_QUESTION_AJOUTER,
                            FaqPrivileges::FAQ_QUESTION_MODIFIER,
                            FaqPrivileges::FAQ_QUESTION_SUPPRIMER,
                        ],
                        'resources' => [Faq::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\FaqQuestion',
                    ],
                    [
                        'privileges' => [
                            FaqPrivileges::FAQ_CATEGORIE_AFFICHER,
                            FaqPrivileges::FAQ_CATEGORIE_AJOUTER,
                            FaqPrivileges::FAQ_CATEGORIE_MODIFIER,
                            FaqPrivileges::FAQ_CATEGORIE_SUPPRIMER,
                        ],
                        'resources' => [FaqCategorieQuestion::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\FaqCategorie',
                    ],
                    [
                        'privileges' => [
                            MessagePrivilege::MESSAGE_INFO_AFFICHER,
                            MessagePrivilege::MESSAGE_INFO_AJOUTER,
                            MessagePrivilege::MESSAGE_INFO_MODIFIER,
                            MessagePrivilege::MESSAGE_INFO_SUPPRIMER,
                        ],
                        'resources' => [MessageInfo::RESOURCE_ID, ArrayRessource::RESOURCE_ID],
                        'assertion' => 'Assertion\\MessageInfo',
                    ],
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'faq' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/faq',
                    'defaults' => [
                        'controller' => FaqQuestionController::class,
                        'action' => FaqQuestionController::ACTION_INDEX
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'question' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/question',
                            'defaults' => [
                                'controller' => FaqQuestionController::class,
                                'action' => FaqQuestionController::ACTION_LISTER
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => FaqQuestionController::class,
                                        'action' => FaqQuestionController::ACTION_LISTER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => FaqQuestionController::class,
                                        'action' => FaqQuestionController::ACTION_AJOUTER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:faq]',
                                    'constraints' => [
                                        'faq' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => FaqQuestionController::class,
                                        'action' => FaqQuestionController::ACTION_MODIFIER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:faq]',
                                    'constraints' => [
                                        'faq' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => FaqQuestionController::class,
                                        'action' => FaqQuestionController::ACTION_SUPPRIMER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                    'categorie' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/categorie',
                            'defaults' => [
                                'controller' => FaqCategorieController::class,
                                'action' => FaqCategorieController::ACTION_INDEX
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'lister' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/lister',
                                    'defaults' => [
                                        'controller' => FaqCategorieController::class,
                                        'action' => FaqCategorieController::ACTION_LISTER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'ajouter' => [
                                'type' => Literal::class,
                                'options' => [
                                    'route' => '/ajouter',
                                    'defaults' => [
                                        'controller' => FaqCategorieController::class,
                                        'action' => FaqCategorieController::ACTION_AJOUTER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'modifier' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/modifier[/:faqCategorieQuestion]',
                                    'constraints' => [
                                        'faqCategorieQuestion' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => FaqCategorieController::class,
                                        'action' => FaqCategorieController::ACTION_MODIFIER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                            'supprimer' => [
                                'type' => Segment::class,
                                'options' => [
                                    'route' => '/supprimer[/:faqCategorieQuestion]',
                                    'constraints' => [
                                        'faqCategorieQuestion' => '[0-9]+',
                                    ],
                                    'defaults' => [
                                        'controller' => FaqCategorieController::class,
                                        'action' => FaqCategorieController::ACTION_SUPPRIMER
                                    ],
                                ],
                                'may_terminate' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'message' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/message',
                    'defaults' => [
                        'controller' => MessageInfoController::class,
                        'action' => MessageInfoController::ACTION_INDEX,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'lister' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/lister',
                            'defaults' => [
                                'controller' => MessageInfoController::class,
                                'action' => MessageInfoController::ACTION_LISTER,
                            ],
                        ],
                    ],
                    'afficher' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/afficher[/:messageInfo]',
                            'constraints' => [
                                'messageInfo' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => MessageInfoController::class,
                                'action' => MessageInfoController::ACTION_AFFICHER,
                            ],
                        ],
                    ],
                    'ajouter' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/ajouter',
                            'defaults' => [
                                'controller' => MessageInfoController::class,
                                'action' => MessageInfoController::ACTION_AJOUTER,
                            ],
                        ],
                    ],
                    'modifier' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/modifier[/:messageInfo]',
                            'constraints' => [
                                'messageInfo' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => MessageInfoController::class,
                                'action' => MessageInfoController::ACTION_MODIFIER,
                            ],
                        ],
                    ],
                    'supprimer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/supprimer[/:messageInfo]',
                            'constraints' => [
                                'messageInfo' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => MessageInfoController::class,
                                'action' => MessageInfoController::ACTION_SUPPRIMER,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            FaqQuestionController::class => FaqQuestionControllerFactory::class,
            FaqCategorieController::class => FaqCategorieControllerFactory::class,
            MessageInfoController::class => MessageInfoControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            FaqService::class => FaqServiceFactory::class,
            FaqCategorieQuestionService::class => FaqCategorieQuestionServiceFactory::class,
            MessageInfoService::class => MessageInfoServiceFactory::class,
        ],
    ],

    'form_elements' => [
        'factories' => [
            FaqQuestionForm::class => FaqQuestionFormFactory::class,
            FaqQuestionFieldset::class => FaqQuestionFieldsetFactory::class,
            FaqCategorieQuestionForm::class => FaqCategorieQuestionFormFactory::class,
            FaqCategorieQuestionFieldset::class => FaqCategorieQuestionFieldsetFactory::class,
            MessageInfoForm::class => MessageInfoFormFactory::class,
            MessageInfoFieldset::class => MessageInfoFieldsetFactory::class,
        ],
    ],
    'hydrators' => [
        'factories' => [
            FaqQuestionHydrator::class => FaqQuestionHydratorFactory::class,
            MessageInfoHydrator::class => MessageInfoHydratorFactory::class
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'faq' => FAQViewHelper::class,
            'categorieFaq' => FAQCategorieQuestionViewHelper::class,
            'messageInfo' => MessageInfoViewHelper::class,
        ],
        'invokables' => [
            'faq' => FAQViewHelper::class,
            'categorieFaq' => FAQCategorieQuestionViewHelper::class,
        ],
        'factories' => [
            MessageInfoViewHelper::class => MessageInfoViewHelperFactory::class
        ]
    ],
];