<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_cursus_etudiant_et_contrainte_cursus_etudiant_i_fkey',
    'table'       => 'contrainte_cursus_etudiant_etat_linker',
    'rtable'      => 'contrainte_cursus_etudiant',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contrainte_cursus_etudiant_pkey',
    'columns'     => [
        'contrainte_cursus_etudiant_id' => 'id',
    ],
];

//@formatter:on
