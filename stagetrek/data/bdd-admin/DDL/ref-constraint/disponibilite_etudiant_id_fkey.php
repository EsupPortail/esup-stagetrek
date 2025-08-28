<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'disponibilite_etudiant_id_fkey',
    'table'       => 'disponibilite',
    'rtable'      => 'etudiant',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'etudiant_pkey',
    'columns'     => [
        'etudiant_id' => 'id',
    ],
];

//@formatter:on
