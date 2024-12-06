<?php

use Application\View\Helper\Timeline\TimelineHelper;
use Laminas\ServiceManager\Factory\InvokableFactory;

// ToDo : faire un module à part pour la Timeline

return [
    'view_helpers' => [
        'aliases' => [
            'timeline' => TimelineHelper::class,
        ],
        'factories' => [
            TimelineHelper::class => InvokableFactory::class,
        ],
    ],
];
