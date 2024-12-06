<?php

return [
    // Module [Unicaen]Auth
    'unicaen-auth' => [
        'cas' => [
            'connection' => [
                'default' => [
                    'params' => [
                        'hostname' => ($_ENV['CAS_HOST']) ?? "",
                        'port'     => (isset($_ENV['CAS_PORT'])) ? intval($_ENV['CAS_PORT']) : 0,
                        'version'  => ($_ENV['CAS_VERSION']) ?? "",
                        'uri'      => "",
                        'debug'    => false,
                    ],
                ],
            ],
        ],
    ],
];