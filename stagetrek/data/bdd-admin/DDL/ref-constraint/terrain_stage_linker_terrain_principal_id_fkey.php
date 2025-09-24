<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'terrain_stage_linker_terrain_principal_id_fkey',
    'table'       => 'terrain_stage_linker',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_principal_id' => 'id',
    ],
];

//@formatter:on
