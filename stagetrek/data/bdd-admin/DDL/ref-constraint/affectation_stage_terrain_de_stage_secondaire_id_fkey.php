<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'affectation_stage_terrain_de_stage_secondaire_id_fkey',
    'table'       => 'affectation_stage',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_secondaire_id' => 'id',
    ],
];

//@formatter:on
