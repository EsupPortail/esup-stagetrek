<?php

namespace Console;

use Console\Service\Utilisateur\AddRoleCommand;
use Console\Service\Utilisateur\CreateUserCommand;
use Console\Service\Utilisateur\Factory\AddRoleCommandFactory;
use Console\Service\Utilisateur\Factory\CreateUserCommandFactory;
use Console\Service\Utilisateur\Factory\RemoveRoleCommandFactory;
use Console\Service\Utilisateur\Factory\RemoveUserCommandFactory;
use Console\Service\Utilisateur\RemoveRoleCommand;
use Console\Service\Utilisateur\RemoveUserCommand;

return [
    'laminas-cli' => [
        'commands' => [
            'utilisateur:add-user' => CreateUserCommand::class,
            'utilisateur:remove-user' => RemoveUserCommand::class,
            'utilisateur:add-role' => AddRoleCommand::class,
            'utilisateur:remove-role' => RemoveRoleCommand::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            CreateUserCommand::class => CreateUserCommandFactory::class,
            RemoveUserCommand::class => RemoveUserCommandFactory::class,
            AddRoleCommand::class => AddRoleCommandFactory::class,
            RemoveRoleCommand::class => RemoveRoleCommandFactory::class,
        ]
    ],
];