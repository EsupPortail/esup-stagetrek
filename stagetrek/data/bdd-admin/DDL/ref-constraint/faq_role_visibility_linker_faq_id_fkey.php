<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'faq_role_visibility_linker_faq_id_fkey',
    'table'       => 'faq_role_visibility_linker',
    'rtable'      => 'faq',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'faq_pkey',
    'columns'     => [
        'faq_id' => 'id',
    ],
];

//@formatter:on
