<?php

//@formatter:off

return [
    'name'    => 'session_terrain_linker_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'session_stage_terrain_linker',
    'schema'  => 'public',
    'columns' => [
        'session_stage_id',
        'terrain_stage_id',
    ],
];

//@formatter:on
