<?php

use Application\Provider\Misc\EnvironnementProvider;

return [

//    API
    'StageTrek' => [
        'http_client' => [
            'api' => [
                'geo_gouv' => [
                    'url' => 'https://geo.api.gouv.fr',
                    'use_proxy' => true,
                ],
            ],
        ],
    ],
];

