<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'session_stage_terrain_linker_terrain_stage_id_fkey',
    'table'       => 'session_stage_terrain_linker',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
