<?php

//@formatter:off

return [
    'name'    => 'contact_stage_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contact_stage',
    'schema'  => 'public',
    'columns' => [
        'contact_id',
        'stage_id',
    ],
];

//@formatter:on
