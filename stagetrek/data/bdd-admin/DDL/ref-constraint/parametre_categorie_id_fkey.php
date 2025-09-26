<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'parametre_categorie_id_fkey',
    'table'       => 'parametre',
    'rtable'      => 'parametre_categorie',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'parametre_categorie_pkey',
    'columns'     => [
        'categorie_id' => 'id',
    ],
];

//@formatter:on
