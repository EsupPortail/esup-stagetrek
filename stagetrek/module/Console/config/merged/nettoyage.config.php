<?php

namespace Console;

use Console\Service\Nettoyage\CleanDataCommand;
use Console\Service\Nettoyage\Factory\CleanDataCommandFactory;
use Console\Service\Nettoyage\RemoveEtudiantCommand;
return [
    'laminas-cli' => [
        'commands' => [
            'data:clear' => CleanDataCommand::class,
            'etudiant:delete' => RemoveEtudiantCommand::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            CleanDataCommand::class => CleanDataCommandFactory::class,
        ]
    ],
];