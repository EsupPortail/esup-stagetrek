<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'terrain_stage_niveau_demande_fk',
    'table'       => 'session_stage_terrain_linker',
    'rtable'      => 'terrain_stage_niveau_demande',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'terrain_stage_niveau_demande_pkey',
    'columns'     => [
        'terrain_stage_niveau_demande_id' => 'id',
    ],
];

//@formatter:on
