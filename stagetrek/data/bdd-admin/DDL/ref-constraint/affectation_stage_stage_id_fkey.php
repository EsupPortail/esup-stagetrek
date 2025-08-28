<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'affectation_stage_stage_id_fkey',
    'table'       => 'affectation_stage',
    'rtable'      => 'stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'stage_pkey',
    'columns'     => [
        'stage_id' => 'id',
    ],
];

//@formatter:on
