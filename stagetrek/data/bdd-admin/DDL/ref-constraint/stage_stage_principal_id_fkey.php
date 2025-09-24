<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'stage_stage_principal_id_fkey',
    'table'       => 'stage',
    'rtable'      => 'stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'stage_pkey',
    'columns'     => [
        'stage_principal_id' => 'id',
    ],
];

//@formatter:on
