<?php

use Application\Provider\Misc\EnvironnementProvider;

$refEtudiant = [];
if(isset($_ENV['REF_ETUDIANT_SOURCE_CODE']) && $_ENV['REF_ETUDIANT_SOURCE_CODE'] != ""){
    $refEtudiant = [
        'source_code' => $_ENV['REF_ETUDIANT_SOURCE_CODE'],
        'url' => ($_ENV['REF_ETUDIANT_API_URL'])?? "",
        'token' => ($_ENV['REF_ETUDIANT_API_TOKEN'])?? "",
        'use_proxy' => (isset($_ENV['PROXY_HOST'])) ? true : false,
    ];
}
return [

//    API
    'StageTrek' => [
        'http_client' => [
            'api' => [
                'geo_gouv' => [
                    'url' => 'https://geo.api.gouv.fr',
                    'use_proxy' => true,
                ],
                'referentiel_etudiant' => $refEtudiant,
            ],
        ],
    ],
];

