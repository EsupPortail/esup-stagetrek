<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'preference_terrain_stage_id_fkey',
    'table'       => 'preference',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
