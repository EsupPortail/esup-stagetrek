<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'stage_session_stage_id_fkey',
    'table'       => 'stage',
    'rtable'      => 'session_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'session_stage_pkey',
    'columns'     => [
        'session_stage_id' => 'id',
    ],
];

//@formatter:on
