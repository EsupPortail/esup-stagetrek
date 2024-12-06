<?php

use Application\Provider\Misc\EnvironnementProvider;

$refEtudiant = [];
if(isset($_ENV['REF_ETUDIANT_SOURCE_CODE']) && $_ENV['REF_ETUDIANT_SOURCE_CODE'] != ""){
    $refEtudiant = [
        'source_code' => $_ENV['REF_ETUDIANT_SOURCE_CODE'],
        'url' => ($_ENV['REF_ETUDIANT_API_URL'])?? "",
        'token' => ($_ENV['REF_ETUDIANT_API_TOKEN'])?? "",
        'use_proxy' => (isset($_ENV['PROXY_HOST'])) ? true : false,
        'data' => [
            'num_etu' => (isset($_ENV['REF_ETUDIANT_DATA_NUM_ETU']) && $_ENV['REF_ETUDIANT_DATA_NUM_ETU'] != "") ? ($_ENV['REF_ETUDIANT_DATA_NUM_ETU']) : 'num_etu',
            'nom' => (isset($_ENV['REF_ETUDIANT_DATA_NOM']) && $_ENV['REF_ETUDIANT_DATA_NOM'] != "") ? ($_ENV['REF_ETUDIANT_DATA_NOM']) : 'nom',
            'prenom' => (isset($_ENV['REF_ETUDIANT_DATA_PRENOM']) && $_ENV['REF_ETUDIANT_DATA_PRENOM'] != "") ?  ($_ENV['REF_ETUDIANT_DATA_PRENOM']) : 'prenom',
            'email' => (isset($_ENV['REF_ETUDIANT_DATA_EMAIL']) && $_ENV['REF_ETUDIANT_DATA_EMAIL'] != "") ?  ($_ENV['REF_ETUDIANT_DATA_EMAIL']) : 'email',
            'date_naissance' => (isset($_ENV['REF_ETUDIANT_DATA_DATE_NAISSANCE']) && $_ENV['REF_ETUDIANT_DATA_DATE_NAISSANCE'] != "") ? ($_ENV['REF_ETUDIANT_DATA_DATE_NAISSANCE']) : 'date_naissance',
        ],
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

