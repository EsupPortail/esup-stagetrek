<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_privilege_privilege_role_linker_privilege_id_fk',
    'table'       => 'unicaen_privilege_privilege_role_linker',
    'rtable'      => 'unicaen_privilege_privilege',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'privilege_pkey',
    'columns'     => [
        'privilege_id' => 'id',
    ],
];

//@formatter:on
