<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'parametre_terrain_cout_affectation_fix_terrain_stage_id_fkey',
    'table'       => 'parametre_terrain_cout_affectation_fixe',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
