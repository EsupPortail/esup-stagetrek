<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'annee_universitaire_etat_linker_annee_universitaire_id_fkey',
    'table'       => 'annee_universitaire_etat_linker',
    'rtable'      => 'annee_universitaire',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'annee_universitaire_pkey',
    'columns'     => [
        'annee_universitaire_id' => 'id',
    ],
];

//@formatter:on
