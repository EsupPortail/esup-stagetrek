<?php

namespace Console;

use Console\Service\Update\Factory\UpdateEntitiesCommandFactory;
use Console\Service\Update\UpdateAffectationCommand;
use Console\Service\Update\UpdateAnneeCommand;
use Console\Service\Update\UpdateContactCommand;
use Console\Service\Update\UpdateContraintesCommand;
use Console\Service\Update\UpdateConventionStageCommand;
use Console\Service\Update\UpdateEtudiantCommand;
use Console\Service\Update\UpdateOrdreAffectationCommand;
use Console\Service\Update\UpdatePreferenceCommand;
use Console\Service\Update\UpdateSessionCommand;
use Console\Service\Update\UpdateStageCommand;
use Console\Service\Update\UpdateValidationStageCommand;

return [
    'laminas-cli' => [
        'commands' => [
            //Note : ne pas faire de commande UpdateEntities car trop de données possibles
            'update:affectations' => UpdateAffectationCommand::class,
            'update:annees' => UpdateAnneeCommand::class,
            'update:contraintes' => UpdateContraintesCommand::class,
            'update:conventions-stages' => UpdateConventionStageCommand::class,
            'update:contacts' => UpdateContactCommand::class,
            'update:etudiants' => UpdateEtudiantCommand::class,
            'update:preferences' => UpdatePreferenceCommand::class,
            'update:sessions' => UpdateSessionCommand::class,
            'update:stages' => UpdateStageCommand::class,
            'update:ordres-affectations' => UpdateOrdreAffectationCommand::class,
            'update:validations-stages' => UpdateValidationStageCommand::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            UpdateAnneeCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateAffectationCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateContactCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateContraintesCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateConventionStageCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateEtudiantCommand::class => UpdateEntitiesCommandFactory::class,
            UpdatePreferenceCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateSessionCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateStageCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateOrdreAffectationCommand::class => UpdateEntitiesCommandFactory::class,
            UpdateValidationStageCommand::class => UpdateEntitiesCommandFactory::class,
        ]
    ],
];