<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_terrain_stage_niveau_etude_terrain_stage_id_fkey',
    'table'       => 'contrainte_terrain_stage_niveau_etude_linker',
    'rtable'      => 'terrain_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'terrain_stage_pkey',
    'columns'     => [
        'terrain_stage_id' => 'id',
    ],
];

//@formatter:on
