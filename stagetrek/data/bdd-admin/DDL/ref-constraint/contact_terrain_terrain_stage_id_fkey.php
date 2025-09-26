<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contact_terrain_terrain_stage_id_fkey',
    'table'       => 'contact_terrain',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
