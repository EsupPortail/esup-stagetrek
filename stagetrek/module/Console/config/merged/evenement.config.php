<?php

namespace Console;

use Console\Service\Evenement\EvenementUpdateCommand;
use Console\Service\Evenement\Factory\EvenementGenererCommandFactory;
use Console\Service\Evenement\Factory\EvenementTraiterCommandFactory;
use Console\Service\Evenement\EvenementGenererCommand;
use Console\Service\Evenement\EvenementTraiterCommand;
use Console\Service\Evenement\Factory\EvenementUpdateCommandFactory;

return [
    'laminas-cli' => [
        'commands' => [
            'evenement:generer' => EvenementGenererCommand::class,
            'evenement:traiter' => EvenementTraiterCommand::class,
            'evenement:update' => EvenementUpdateCommand::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            EvenementGenererCommand::class => EvenementGenererCommandFactory::class,
            EvenementTraiterCommand::class => EvenementTraiterCommandFactory::class,
            EvenementUpdateCommand::class => EvenementUpdateCommandFactory::class,
        ]
    ],
];