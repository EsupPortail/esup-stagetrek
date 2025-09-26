<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'adresse_adresse_type_id_fkey',
    'table'       => 'adresse',
    'rtable'      => 'adresse_type',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'adresse_type_pkey',
    'columns'     => [
        'adresse_type_id' => 'id',
    ],
];

//@formatter:on
