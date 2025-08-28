<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'message_info_role_visibility_linker_message_id_fkey',
    'table'       => 'message_info_role_visibility_linker',
    'rtable'      => 'message_info',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'message_info_pkey',
    'columns'     => [
        'message_id' => 'id',
    ],
];

//@formatter:on
