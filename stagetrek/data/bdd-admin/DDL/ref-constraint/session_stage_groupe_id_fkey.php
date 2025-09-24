<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'session_stage_groupe_id_fkey',
    'table'       => 'session_stage',
    'rtable'      => 'groupe',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'groupe_pkey',
    'columns'     => [
        'groupe_id' => 'id',
    ],
];

//@formatter:on
