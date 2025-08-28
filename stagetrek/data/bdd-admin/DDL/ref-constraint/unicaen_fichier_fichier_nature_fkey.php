<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_fichier_fichier_nature_fkey',
    'table'       => 'unicaen_fichier_fichier',
    'rtable'      => 'unicaen_fichier_nature',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'unicaen_fichier_nature_pkey',
    'columns'     => [
        'nature' => 'id',
    ],
];

//@formatter:on
