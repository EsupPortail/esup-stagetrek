<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c] 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Assertion\AbstractAssertionFactory;
use Application\Controller\Index\IndexController;
use Application\Controller\Index\IndexControllerFactory;
use Application\Form\Adresse\Factory\AdresseFieldsetFactory;
use Application\Form\Adresse\Fieldset\AdresseFieldset;
use Application\Form\Misc\ConfirmationForm;
use Application\Form\Misc\Factory\ConfirmationFormFactory;
use Application\Form\Misc\Factory\ImportFormFactory;
use Application\Form\Misc\ImportForm;
use Application\Form\Misc\View\Helper\FormControlText;
use Application\Service\Mail\Factory\MailServiceFactory;
use Application\Service\Mail\MailService;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Entity\ModuleOptions;
use Application\Service\Misc\Factory\CsvServiceFactory;
use Application\Service\Misc\Factory\ModuleOptionsFactory;
use Application\Service\Renderer\AdresseRendererService;
use Application\Service\Renderer\ContactRendererService;
use Application\Service\Renderer\DateRendererService;
use Application\Service\Renderer\Factory\AdresseRendererServiceFactory;
use Application\Service\Renderer\Factory\ContactRendererServiceFactory;
use Application\Service\Renderer\Factory\DateRendererServiceFactory;
use Application\Service\Renderer\Factory\MacroServiceFactory;
use Application\Service\Renderer\Factory\ParametreRendererServiceFactory;
use Application\Service\Renderer\Factory\PdfRendererServiceFactory;
use Application\Service\Renderer\Factory\UrlServiceFactory;
use Application\Service\Renderer\MacroService;
use Application\Service\Renderer\ParametreRendererService;
use Application\Service\Renderer\PdfRendererService;
use Application\Service\Renderer\UrlService;
use Application\View\Helper\BackButtonViewHelper;
use Application\View\Helper\Misc\AlertFlashViewHelper;
use Application\View\Helper\Misc\ConfirmationFormViewHelper;
use Application\View\Helper\Misc\Factory\AlertFlashViewHelperFactory;
use Application\View\Helper\Misc\PopAjaxConfirmationViewHelper;
use Application\View\Renderer\FlashMessageDisplayViewHelper;
use Application\View\Renderer\FlashMessageViewHelper;
use Application\View\Renderer\FlashMessageViewHelperFactory;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Laminas\Router\Http\Literal;
use UnicaenPrivilege\Guard\PrivilegeController;

return [
    'bjyauthorize' => [
        'guards' => [
            PrivilegeController::class => [
                [
                    'controller' => IndexController::class,
                    'action' => [
                        IndexController::ACTION_INDEX,
                        'flash-message',
                    ],
                    'roles' => 'guest'
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action' => IndexController::ACTION_INDEX,
                    ],
                    'may_terminate' => true,
                ],
            ],
            'flash-message' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/flash-message',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action' => 'flash-message',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            CSVService::class => CsvServiceFactory::class,
            MailService::class => MailServiceFactory::class,
            MacroService::class => MacroServiceFactory::class,
            ModuleOptions::class => ModuleOptionsFactory::class,
            UrlService::class => UrlServiceFactory::class,
            DateRendererService::class => DateRendererServiceFactory::class,
            AdresseRendererService::class => AdresseRendererServiceFactory::class,
            ContactRendererService::class => ContactRendererServiceFactory::class,
            PdfRendererService::class => PdfRendererServiceFactory::class,
            ParametreRendererService::class => ParametreRendererServiceFactory::class,
        ],
        'abstract_factories' => [
            AbstractAssertionFactory::class,
        ]
    ],

    /**
     * Formulaire
     */
    'form_elements' => [
        'factories' => [
            AdresseFieldset::class => AdresseFieldsetFactory::class,
            ConfirmationForm::class => ConfirmationFormFactory::class,
            ImportForm::class => ImportFormFactory::class,
        ],
    ],
    /** Validators */
    'validators' => [
        'factories' => [
        ]
    ],

    'translator' => [
        'locale' => 'fr_FR', // en_US
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            IndexController::class => IndexControllerFactory::class,
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'alertFlash' => AlertFlashViewHelper::class,
            'confirmation' => ConfirmationFormViewHelper::class,
            'backButton' => BackButtonViewHelper::class,
            'flashMessage' => FlashMessageViewHelper::class,
        ],
        'invokables' => [
            'popAjaxConfirmation' => PopAjaxConfirmationViewHelper::class,
            'confirmation' => ConfirmationFormViewHelper::class,
            'formControlText' => FormControlText::class,
            'backButton' => BackButtonViewHelper::class,
            'flashMessageDisplay' => FlashMessageDisplayViewHelper::class,
        ],
        'factories' => [
            AlertFlashViewHelper::class => AlertFlashViewHelperFactory::class,
            FlashMessageViewHelper::class => FlashMessageViewHelperFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'orm_default_xml_driver' => [
                'class' => XmlDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity/Db/Mapping']
            ],
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'Application\Entity\Db' => 'orm_default_xml_driver',
                ],
            ],
        ],
    ],
];