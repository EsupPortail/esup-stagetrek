<?php

namespace Evenement;

use Evenement\Provider\TypeEvenementProvider;
use Evenement\Service\Evenement\EvenementService;
use Evenement\Service\Evenement\Factory\EvenementServiceFactory;
use Evenement\Service\MailAuto\Factory\MailAutoEvenementServiceFactory;
use Evenement\Service\MailAuto\MailAutoAffectationEvenementService;
use Evenement\Service\MailAuto\MailAutoStageDebutChoixEvenementService;
use Evenement\Service\MailAuto\MailAutoStageDebutValidation;
use Evenement\Service\MailAuto\MailAutoStageRappelChoixEvenementService;
use Evenement\Service\MailAuto\MailAutoStageRappelValidationEvenementService;
use Evenement\Service\MailAuto\MailAutoStageValidationEffectueEvenementService;
use Evenement\View\Helper\TypeViewHelper;

return [
    'bjyauthorize' => [
        'guards' => [
        ],
    ],

    'router'
    => [
        'routes' => [
        ],
    ],

    'service_manager' => [
        'factories' => [
            EvenementService::class => EvenementServiceFactory::class,
            MailAutoStageDebutChoixEvenementService::class => MailAutoEvenementServiceFactory::class,
            MailAutoStageRappelChoixEvenementService::class => MailAutoEvenementServiceFactory::class,
            MailAutoStageDebutValidation::class => MailAutoEvenementServiceFactory::class,
            MailAutoStageRappelValidationEvenementService::class => MailAutoEvenementServiceFactory::class,
            MailAutoStageValidationEffectueEvenementService::class => MailAutoEvenementServiceFactory::class,
            MailAutoAffectationEvenementService::class => MailAutoEvenementServiceFactory::class,
        ],
    ],

    'unicaen-evenement' => [
        'service' => [
            // Évènements de base
            TypeEvenementProvider::MAIL_AUTO_STAGE_DEBUT_CHOIX => MailAutoStageDebutChoixEvenementService::class,
            TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX => MailAutoStageRappelChoixEvenementService::class,
            TypeEvenementProvider::MAIL_AUTO_AFFECTATION_VALIDEE => MailAutoAffectationEvenementService::class,
            TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE => MailAutoStageDebutValidation::class,
            TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION => MailAutoStageRappelValidationEvenementService::class,
            TypeEvenementProvider::MAIL_AUTO_STAGE_VALIDATION_EFFECTUE => MailAutoStageValidationEffectueEvenementService::class,
        ],
    ],

    'view_helpers' => [
        'invokables' => [
//            Pour l'affichage des événement
            'type' => TypeViewHelper::class,
        ],
    ],

];