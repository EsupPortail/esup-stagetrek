<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contact_stage_contact_id_fkey',
    'table'       => 'contact_stage',
    'rtable'      => 'contact',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'contact_pkey',
    'columns'     => [
        'contact_id' => 'id',
    ],
];

//@formatter:on
