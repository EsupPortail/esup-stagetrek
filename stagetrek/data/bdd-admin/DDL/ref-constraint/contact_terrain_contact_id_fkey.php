<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contact_terrain_contact_id_fkey',
    'table'       => 'contact_terrain',
    'rtable'      => 'contact',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'contact_pkey',
    'columns'     => [
        'contact_id' => 'id',
    ],
];

//@formatter:on
