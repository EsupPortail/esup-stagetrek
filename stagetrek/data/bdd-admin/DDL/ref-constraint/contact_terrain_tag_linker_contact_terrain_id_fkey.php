<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contact_terrain_tag_linker_contact_terrain_id_fkey',
    'table'       => 'contact_terrain_tag_linker',
    'rtable'      => 'contact_terrain',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contact_terrain_pkey',
    'columns'     => [
        'contact_terrain_id' => 'id',
    ],
];

//@formatter:on
