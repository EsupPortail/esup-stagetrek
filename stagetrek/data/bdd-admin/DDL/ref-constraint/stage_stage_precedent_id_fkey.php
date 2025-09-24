<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'stage_stage_precedent_id_fkey',
    'table'       => 'stage',
    'rtable'      => 'stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'stage_pkey',
    'columns'     => [
        'stage_precedent_id' => 'id',
    ],
];

//@formatter:on
