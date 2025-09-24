<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'etudiant_etat_linker_etudiant_id_fkey',
    'table'       => 'etudiant_etat_linker',
    'rtable'      => 'etudiant',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'etudiant_pkey',
    'columns'     => [
        'etudiant_id' => 'id',
    ],
];

//@formatter:on
