<?php
namespace Application;

use Application\Form\Misc\Element\RoleSelectPicker;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\View\Helper\Parametres\Factory\ParametreViewHelperFactory;
use Application\View\Helper\Parametres\ParametreCoutAffectationViewHelper;
use Application\View\Helper\Parametres\ParametreCoutTerrainViewHelper;
use Application\View\Helper\Parametres\ParametreViewHelper;
use Laminas\Router\Http\Literal;
use UnicaenMail\Controller\MailController;

return [
    'router' => [
        'routes' => [
            'mail' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/mails',
                    'defaults' => [
                        'controller' => MailController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
            ],
        ],
    ],

    /**
     * Controller et Factory
     * Utiliser InvokableFactory comme factory générique
     */
    'controllers' => [
        'factories' => [
         ],
    ],
    /**
     * Formulaire
     */
    'form_elements' => [
        'factories' => [
            RoleSelectPicker::class => SelectPickerFactory::class,
        ],
    ],
    'validators' => [
        'factories' => [
        ],
    ],
    'hydrators' => [
        'factories' => [
        ],
    ],
    'service_manager' => [
        'factories' => [
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'parametre' => ParametreViewHelper::class,
            'parametreCoutAffectation' => ParametreCoutAffectationViewHelper::class,
            'parametreCoutTerrain' => ParametreCoutTerrainViewHelper::class,
        ],
        'invokables' => [
            ParametreCoutAffectationViewHelper::class => ParametreCoutAffectationViewHelper::class,
            ParametreCoutTerrainViewHelper::class => ParametreCoutTerrainViewHelper::class,
        ],
        'factories' => [
            ParametreViewHelper::class => ParametreViewHelperFactory::class,
        ]
    ],
];


