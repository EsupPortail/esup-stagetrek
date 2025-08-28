<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'parametre_parametre_type_id_fkey',
    'table'       => 'parametre',
    'rtable'      => 'parametre_type',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'parametre_type_pkey',
    'columns'     => [
        'parametre_type_id' => 'id',
    ],
];

//@formatter:on
