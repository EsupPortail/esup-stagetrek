<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'session_stage_session_stage_precedente_id_fkey',
    'table'       => 'session_stage',
    'rtable'      => 'session_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'session_stage_pkey',
    'columns'     => [
        'session_stage_precedente_id' => 'id',
    ],
];

//@formatter:on
