<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'session_stage_etudiant_linker_etudiant_id_fkey',
    'table'       => 'session_stage_etudiant_linker',
    'rtable'      => 'etudiant',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'etudiant_pkey',
    'columns'     => [
        'etudiant_id' => 'id',
    ],
];

//@formatter:on
