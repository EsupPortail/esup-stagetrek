<?php

//@formatter:off

return [
    'name'    => 'message_info_role_visibility_linker_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'message_info_role_visibility_linker',
    'schema'  => 'public',
    'columns' => [
        'message_id',
        'role_id',
    ],
];

//@formatter:on
