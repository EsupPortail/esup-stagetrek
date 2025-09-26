<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'groupe_groupe_suivant_id_fkey',
    'table'       => 'groupe',
    'rtable'      => 'groupe',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'groupe_pkey',
    'columns'     => [
        'groupe_suivant_id' => 'id',
    ],
];

//@formatter:on
