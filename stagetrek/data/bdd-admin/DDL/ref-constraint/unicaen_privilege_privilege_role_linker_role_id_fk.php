<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_privilege_privilege_role_linker_role_id_fk',
    'table'       => 'unicaen_privilege_privilege_role_linker',
    'rtable'      => 'unicaen_utilisateur_role',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'role_pkey',
    'columns'     => [
        'role_id' => 'id',
    ],
];

//@formatter:on
