<?php

//@formatter:off

return [
    'name'    => 'faq_role_visibility_linker_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'faq_role_visibility_linker',
    'schema'  => 'public',
    'columns' => [
        'faq_id',
        'role_id',
    ],
];

//@formatter:on
