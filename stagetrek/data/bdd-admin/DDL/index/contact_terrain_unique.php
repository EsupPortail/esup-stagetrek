<?php

//@formatter:off

return [
    'name'    => 'contact_terrain_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contact_terrain',
    'schema'  => 'public',
    'columns' => [
        'contact_id',
        'terrain_stage_id',
    ],
];

//@formatter:on
