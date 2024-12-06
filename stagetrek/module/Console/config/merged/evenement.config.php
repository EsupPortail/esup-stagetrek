<?php

namespace Console;

use Console\Service\Evenement\Factory\GenererEvenementsCommandFactory;
use Console\Service\Evenement\Factory\TraiterEvenementsCommandFactory;
use Console\Service\Evenement\GenererEvenementCommand;
use Console\Service\Evenement\TraiterEvenementCommand;

return [
    'laminas-cli' => [
        'commands' => [
            'generer-evenements' => GenererEvenementCommand::class,
            'traiter-evenements' => TraiterEvenementCommand::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            GenererEvenementCommand::class => GenererEvenementsCommandFactory::class,
            TraiterEvenementCommand::class => TraiterEvenementsCommandFactory::class,
        ]
    ],
];