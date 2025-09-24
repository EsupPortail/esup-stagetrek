<?php

//@formatter:off

return [
    'name'    => 'unicaen_tag_code_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'unicaen_tag',
    'schema'  => 'public',
    'columns' => [
        'code',
    ],
];

//@formatter:on
