<?php

namespace Indicateur;

use Indicateur\Service\Perimetre\PerimetreService;
use Indicateur\Service\Perimetre\PerimetreServiceFactory;

return [
    'bjyauthorize' => [
        'guards' => [
        ],
    ],

    'router'
    => [
        'routes' => [
        ],
    ],

    'service_manager' => [
        'factories' => [
            PerimetreService::class => PerimetreServiceFactory::class,
        ],
    ],
];