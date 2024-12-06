<?php

namespace Console;

use UnicaenPrivilege\Guard\PrivilegeController;

return array(
    'bjyauthorize'    => [
        'guards' => [
            PrivilegeController::class => [],
        ],
    ],
    'doctrine' => [
//        'driver' => [
//            'orm_default' => [
//                'class' => MappingDriverChain::class,
//                'drivers' => [
//                    'Console\Entity\Db' => 'orm_default_xml_driver',
//                ],
//            ],
//            'orm_default_xml_driver' => [
//                'class' => XmlDriver::class,
//                'cache' => 'stagetrek',
//                'paths' => [
//                    __DIR__ . '/../src/Console/Entity/Db/Mapping',
//                ],
//            ],
//        ],
//        'cache' => [
//            'apc' => [
//                'namespace' => 'EVENEMENT__' . __NAMESPACE__,
//            ],
//        ],
    ],
    'console' => [
        'view_helpers' => [
        ]
    ],
);
