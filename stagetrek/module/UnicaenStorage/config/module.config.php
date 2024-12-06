<?php

use UnicaenStorage\StorageAdapterManager;
use UnicaenStorage\StorageAdapterManagerFactory;

return [
    'service_manager' => [
        'factories' => [
            StorageAdapterManager::class => StorageAdapterManagerFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [

        ],
    ],
    'controller_plugins' => [
        'invokables' => [

        ],
    ],
];
