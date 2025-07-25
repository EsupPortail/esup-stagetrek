<?php

use Application\Provider\Misc\EnvironnementProvider;

$config = [
    'console-cli' => [
        //TODO : a gérré
        'report-to' => ($_ENV['CONSOLE_REPORT_TO']) ?? "",
        'console_env' => ($_ENV['CONSOLE_ENV']) ?? (($_ENV['APP_ENV']) ?? EnvironnementProvider::PRODUCTION),

        //Paramètre requis pour la génération automatique d'url depuis une console
        'uri-host' => ($_ENV['URI_HOST']) ?? "",
        'uri-scheme' => ($_ENV['URI_SCHEMA']) ?? "",
//        TODO : voir si l'on remet un controle des données avec un système d'alerte
//    SInon table alerte a supprimer
    ],
];

return $config;
