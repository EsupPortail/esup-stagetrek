<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'stage_tag_linker_stage_id_fkey',
    'table'       => 'stage_tag_linker',
    'rtable'      => 'stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'stage_pkey',
    'columns'     => [
        'stage_id' => 'id',
    ],
];

//@formatter:on
