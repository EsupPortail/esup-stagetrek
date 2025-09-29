<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contact_stage_tag_linker_contact_stage_id_fkey',
    'table'       => 'contact_stage_tag_linker',
    'rtable'      => 'contact_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contact_stage_pkey',
    'columns'     => [
        'contact_stage_id' => 'id',
    ],
];

//@formatter:on
