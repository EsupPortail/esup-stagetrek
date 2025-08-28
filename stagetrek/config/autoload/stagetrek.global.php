<?php

use Application\Provider\Misc\EnvironnementProvider;
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;

return [
    'unicaen-app' => [
        /**
         * Informations concernant cette application
         */
        'app_infos' => [
            'nom'     => "StageTrek",
            'desc'    => "Application de gestion des stages de médecine de second cycle",
            'version' => "1.3.2",
            'date'    => "28/08/2025",
            'contact' => ['mail' => "assistance-stagetrek@unicaen.fr"],
        ],

        'session_refresh_period' => 0, // 0 <=> aucune requête exécutée
        'maintenance' => [
            // activation (TRUE: activé, FALSE: désactivé)
            'enable' => isset($_ENV['MAINTENACE_ACTIVE']) && $_ENV['MAINTENACE_ACTIVE']=="true" ? true : false,
            // message à afficher
            'message' => "L'application est temporairement indisponible pour des raisons de maintenance, veuillez nous excuser pour la gêne occasionnée.",
            // le mode console est-il aussi concerné (TRUE: oui, FALSE: non)
            'include_cli' => isset($_ENV['MAINTENACE_CLI_ACTIVE']) && $_ENV['MAINTENACE_CLI_ACTIVE']=="true" ? true : false,
           // liste blanche des adresses IP clientes à laisser passer
            //TODO : a voir si réelement utile. Si oui, passer en variables d'environnement
            'white_list' => [
                // Formats possibles : [ REMOTE_ADDR ] ou [ REMOTE_ADDR, HTTP_X_FORWARDED_FOR ]
                // exemples :
//                 ['127.0.0.1'], // localhost
//                 ['172.17.0.1'], // Docker container
                // ['195.220.135.97', '194.199.107.33'], // Via proxy
            ],
        ],

        /**
         * Paramétrage pour utilisation pour autorisation ou non à la connexion à
         * une app de l'exterieur de l'établissement
         */
        'hostlocalization' => [
            'activated' => false,
            'proxies' => [
                //xxx.xx.xx.xxx
            ],
            'reverse-proxies' => [
                //xxx.xx.xx.xxx
            ],
            'masque-ip' => '',

        ],

    ],

//    API
    'StageTrek' => [
        'application_env' => ($_ENV['APP_ENV']) ?? EnvironnementProvider::TEST,

        'http_client' => [
            'uri_host' => ($_ENV['URI_HOST']) ?? "",
            'uri_scheme' => ($_ENV['URI_SCHEMA']) ?? "",
//            'proxyhost' => ($_ENV['PROXY_HOST']) ?? "",
//            'proxyport' => ($_ENV['PROXY_PORT']) ?? "",

            'api' => [
                'geo_gouv' => [
                    'url' => 'https://geo.api.gouv.fr',
                    'use_proxy' => true,
                ],
            ],
        ],
    ],

//    Layout de base
    'view_manager' => [
        'display_not_found_reason' =>  (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == EnvironnementProvider::DEVELOPPEMENT) ? true : false,
        'display_exceptions'       =>  (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] == EnvironnementProvider::DEVELOPPEMENT) ? true : false,
        'template_map' => [
            'layout/layout' => realpath('./module/Application/view/layout/layout.phtml'),
        ],
    ],

//Misc

    'translator' => [
        'locale' => 'fr_FR',
    ],

    //
    // Session configuration.
    //
    'session_config' => [
        // Session cookie will expire in 8 hour.
        'cookie_lifetime' => 60*60*8,
        // Durée de vie max des variables de session (mis arbitrairement a 5min)
        'gc_maxlifetime'     => 60*5,

        'cookie_httponly' => 1,
        'use_only_cookies' => 1,
        'cookie_secure' => 1,

    ],
    //
    // Session manager configuration.
    //
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    //
    // Session storage configuration.
    //
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];
