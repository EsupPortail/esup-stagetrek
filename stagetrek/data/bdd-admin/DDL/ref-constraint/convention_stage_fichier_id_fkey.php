<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'convention_stage_fichier_id_fkey',
    'table'       => 'convention_stage',
    'rtable'      => 'unicaen_fichier_fichier',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'unicaen_fichier_fichier_pkey',
    'columns'     => [
        'fichier_id' => 'id',
    ],
];

//@formatter:on
