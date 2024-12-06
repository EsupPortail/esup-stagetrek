<?php
namespace Application;


use Application\Form\Misc\Element\EtatTypeSelectPicker;
use Application\Form\Misc\Factory\SelectPickerFactory;
use Application\Form\Misc\Validator\AcronymeValidator;
use Application\Form\Misc\Validator\AcronymeValidatorFactory;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\CodeValidatorFactory;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Form\Misc\Validator\LibelleValidatorFactory;
use Application\Misc\ArrayRessource;
use Application\ORM\Event\Listeners\Factory\CodeListenerFactory;
use Application\ORM\Event\Listeners\CodeListener;
use Application\Provider\Roles\IdentityProvider;
use Application\Provider\Roles\IdentityProviderFactory;
use Application\Service\Adresse\AdresseService;
use Application\Service\Adresse\AdresseTypeService;
use Application\Service\Adresse\Factory\AdresseServiceFactory;
use Application\Service\Adresse\Factory\AdresseTypeServiceFactory;
use Application\View\Helper\Adresse\AdresseViewHelper;
use Application\View\Helper\Misc\ApplicationViewHelper;
use Application\View\Helper\Misc\Factory\ApplicationViewHelperFactory;
use Application\View\Helper\Misc\Factory\FooterViewHelperFactory;
use Application\View\Helper\Misc\FooterViewHelper;
use UnicaenUtilisateur\ORM\Event\Listeners\HistoriqueListener;

return [
    'bjyauthorize' => [
        'resource_providers' => [
            'BjyAuthorize\Provider\Resource\Config' => [
                ArrayRessource::RESOURCE_ID => [],
            ],
        ],
    ],
    /** Validators */
    'validators' => [
        'factories' => [
            LibelleValidator::class => LibelleValidatorFactory::class,
            CodeValidator::class => CodeValidatorFactory::class,
            AcronymeValidator::class => AcronymeValidatorFactory::class,
        ]
    ],

    'view_helpers' => [
        'aliases' => [
            'app' => ApplicationViewHelper::class,
            'adresse' => AdresseViewHelper::class,
            'footer' => FooterViewHelper::class
        ],
        'invokables' => [
            'adresse' => AdresseViewHelper::class,
        ],
        'factories' => [
            ApplicationViewHelper::class => ApplicationViewHelperFactory::class,
            FooterViewHelper::class => FooterViewHelperFactory::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            IdentityProvider::class => IdentityProviderFactory::class,
            AdresseService::class => AdresseServiceFactory::class,
            AdresseTypeService::class => AdresseTypeServiceFactory::class,
            CodeListener::class => CodeListenerFactory::class,
        ],
    ],
    /**
     * Formulaire
     */
    'form_elements' => [
        'factories' => [
            EtatTypeSelectPicker::class => SelectPickerFactory::class,
        ],
    ],
    'doctrine' => [
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    HistoriqueListener::class,
                    CodeListener::class,
                ],
            ],
        ],
    ],
];


