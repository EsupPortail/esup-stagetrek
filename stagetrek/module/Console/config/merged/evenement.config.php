<?php

namespace Console;

use Console\Service\Evenement\Factory\EvenementGenererCommandFactory;
use Console\Service\Evenement\Factory\EvenementTraiterCommandFactory;
use Console\Service\Evenement\EvenementGenererCommand;
use Console\Service\Evenement\EvenementTraiterCommand;

return [
    'laminas-cli' => [
        'commands' => [
            'evenement:generer' => EvenementGenererCommand::class,
            'evenement:traiter' => EvenementTraiterCommand::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            EvenementGenererCommand::class => EvenementGenererCommandFactory::class,
            EvenementTraiterCommand::class => EvenementTraiterCommandFactory::class,
        ]
    ],
];