<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'affectation_stage_terrain_de_stage_id_fkey',
    'table'       => 'affectation_stage',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
