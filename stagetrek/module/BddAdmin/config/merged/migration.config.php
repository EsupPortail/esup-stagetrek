<?php

namespace BddAdmin;

use BddAdmin\Migration\Migration_20250717_Faq;

return [
    'unicaen-bddadmin' => [
        'migration' => [
            Migration_20250717_Faq::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
        ],
        'invokables' => [
//            Scripts de migrations
            Migration_20250717_Faq::class => Migration_20250717_Faq::class,
        ]
    ],
];