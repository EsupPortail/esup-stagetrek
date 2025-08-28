<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'terrain_stage_adresse_id_fkey',
    'table'       => 'terrain_stage',
    'rtable'      => 'adresse',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'adresse_pkey',
    'columns'     => [
        'adresse_id' => 'id',
    ],
];

//@formatter:on
