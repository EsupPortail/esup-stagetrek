<?php

//@formatter:off

return [
    'name'    => 'contact_code_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contact',
    'schema'  => 'public',
    'columns' => [
        'code',
    ],
];

//@formatter:on
