<?php
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    //Versions BD Local
                    'host' => ($_ENV['DATABASE_HOST']) ?? "Non-configuré",
                    'dbname' => ($_ENV['DATABASE_NAME']) ?? "Non-configuré",
                    'port' => ($_ENV['DATABASE_PORT']) ?? "Non-configuré",
                    'charset' => 'utf8',
                    'user' => ($_ENV['DATABASE_USER']) ?? "Non-configuré",
                    'password' => ($_ENV['DATABASE_PSWD']) ?? "Non-configuré",
                ],
            ],
        ],
    ],
];