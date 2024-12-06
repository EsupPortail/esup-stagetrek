<?php

$settings = [
    'view-dirs'     => [getcwd() . '/code'],
    'templates-dirs' => [getcwd() . '/code/templates'],
    'generator-output-dir' => '/tmp/UnicaenCode',
    'namespaces'           => [
         'services'  => [
            'Application',
         ],
         'forms'     => [
            'Application\Form',
         ],
         'hydrators' => [
            'Application\Hydrator',
         ],
         'entities'  => [
            'Application\Entity\Db',
         ],
    ],
];

return [
    'unicaen-code' => $settings,
];