<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'etudiant_user_id_fkey',
    'table'       => 'etudiant',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'user_pkey',
    'columns'     => [
        'user_id' => 'id',
    ],
];

//@formatter:on
