<?php

namespace BddAdmin;
use BddAdmin\Command\CopyToCommand;
use Unicaen\BddAdmin\Command\CommandFactory;

return [
    'service_manager' => [
        'factories' => [
            CopyToCommand::class => CommandFactory::class,
        ],
    ],
    'laminas-cli' => [
        'commands' => [
            'bddadmin:copy-to' => CopyToCommand::class,
        ],
    ],
];
