<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'faq_role_visibility_linker_role_id_fkey',
    'table'       => 'faq_role_visibility_linker',
    'rtable'      => 'unicaen_utilisateur_role',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'role_pkey',
    'columns'     => [
        'role_id' => 'id',
    ],
];

//@formatter:on
