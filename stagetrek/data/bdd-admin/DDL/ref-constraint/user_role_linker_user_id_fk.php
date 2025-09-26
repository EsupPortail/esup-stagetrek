<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'user_role_linker_user_id_fk',
    'table'       => 'unicaen_utilisateur_role_linker',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'user_pkey',
    'columns'     => [
        'user_id' => 'id',
    ],
];

//@formatter:on
