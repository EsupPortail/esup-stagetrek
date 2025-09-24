<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'affectation_stage_stage_secondaire_id_fkey',
    'table'       => 'affectation_stage',
    'rtable'      => 'stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'stage_pkey',
    'columns'     => [
        'stage_secondaire_id' => 'id',
    ],
];

//@formatter:on
