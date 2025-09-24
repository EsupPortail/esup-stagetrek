<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'annee_universitaire_annee_universitaire_suivante_id_fkey',
    'table'       => 'annee_universitaire',
    'rtable'      => 'annee_universitaire',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'annee_universitaire_pkey',
    'columns'     => [
        'annee_universitaire_suivante_id' => 'id',
    ],
];

//@formatter:on
