<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'annee_tag_linker_tag_id_fkey',
    'table'       => 'annee_tag_linker',
    'rtable'      => 'unicaen_tag',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_tag_pkey',
    'columns'     => [
        'tag_id' => 'id',
    ],
];

//@formatter:on
