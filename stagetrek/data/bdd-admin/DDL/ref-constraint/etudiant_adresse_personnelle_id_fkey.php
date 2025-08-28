<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'etudiant_adresse_personnelle_id_fkey',
    'table'       => 'etudiant',
    'rtable'      => 'adresse',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'adresse_pkey',
    'columns'     => [
        'adresse_personnelle_id' => 'id',
    ],
];

//@formatter:on
