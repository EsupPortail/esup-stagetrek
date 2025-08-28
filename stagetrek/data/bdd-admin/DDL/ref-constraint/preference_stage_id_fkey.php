<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'preference_stage_id_fkey',
    'table'       => 'preference',
    'rtable'      => 'stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'stage_pkey',
    'columns'     => [
        'stage_id' => 'id',
    ],
];

//@formatter:on
