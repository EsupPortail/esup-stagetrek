<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'fk_unicaen_utilisateur_role_parent',
    'table'       => 'unicaen_utilisateur_role',
    'rtable'      => 'unicaen_utilisateur_role',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'role_pkey',
    'columns'     => [
        'parent_id' => 'id',
    ],
];

//@formatter:on
