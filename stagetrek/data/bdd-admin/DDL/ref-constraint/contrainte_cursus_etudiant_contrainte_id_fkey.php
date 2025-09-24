<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_cursus_etudiant_contrainte_id_fkey',
    'table'       => 'contrainte_cursus_etudiant',
    'rtable'      => 'contrainte_cursus',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contrainte_cursus_pkey',
    'columns'     => [
        'contrainte_id' => 'id',
    ],
];

//@formatter:on
