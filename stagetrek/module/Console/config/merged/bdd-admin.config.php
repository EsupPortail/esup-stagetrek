<?php

namespace Console;

use Console\Service\BddAdmin\Migration\MigrationTest;

return [
    'laminas-cli' => [
        'commands' => [
        ],
    ],

    'service_manager' => [
        'factories' => [
        ],
        'invokables' => [
            MigrationTest::class => MigrationTest::class,
        ]
    ],
];