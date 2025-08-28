<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_cursus_etudiant_etudiant_id_fkey',
    'table'       => 'contrainte_cursus_etudiant',
    'rtable'      => 'etudiant',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'etudiant_pkey',
    'columns'     => [
        'etudiant_id' => 'id',
    ],
];

//@formatter:on
