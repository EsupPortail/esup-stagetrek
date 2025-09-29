<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'terrain_stage_tag_linker_terrain_stage_id_fkey',
    'table'       => 'terrain_stage_tag_linker',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
