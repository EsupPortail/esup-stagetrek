<?php

$authService = ($_ENV['AUTH_SERVICE'] && $_ENV['AUTH_SERVICE'] != "") ? $_ENV['AUTH_SERVICE']  : 'db';

$authService = str_replace(' ', '', $authService);
$authService = explode(",", $authService);

foreach ($authService as $authKey){
    $authServicesAllowed[$authKey] = true;
}
//Permet de ne pas charger la configuration cas si ce n'est pas nécessaire
if(!isset($authServicesAllowed['cas'])){return [];}

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