<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contact_tag_linker_contact_id_fkey',
    'table'       => 'contact_tag_linker',
    'rtable'      => 'contact',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contact_pkey',
    'columns'     => [
        'contact_id' => 'id',
    ],
];

//@formatter:on
