<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'groupe_annee_universitaire_id_fkey',
    'table'       => 'groupe',
    'rtable'      => 'annee_universitaire',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'annee_universitaire_pkey',
    'columns'     => [
        'annee_universitaire_id' => 'id',
    ],
];

//@formatter:on
