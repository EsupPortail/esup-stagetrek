<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'session_stage_etat_linker_session_stage_id_fkey',
    'table'       => 'session_stage_etat_linker',
    'rtable'      => 'session_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'session_stage_pkey',
    'columns'     => [
        'session_stage_id' => 'id',
    ],
];

//@formatter:on
