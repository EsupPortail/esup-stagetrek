<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'etudiant_groupe_linker_groupe_id_fkey',
    'table'       => 'etudiant_groupe_linker',
    'rtable'      => 'groupe',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'groupe_pkey',
    'columns'     => [
        'groupe_id' => 'id',
    ],
];

//@formatter:on
