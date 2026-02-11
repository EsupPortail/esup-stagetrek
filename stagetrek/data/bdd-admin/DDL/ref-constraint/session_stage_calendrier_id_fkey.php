<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'session_stage_calendrier_id_fkey',
    'table'       => 'session_stage',
    'rtable'      => 'unicaen_calendrier_calendrier',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'unicaen_calendrier_calendrier_pk',
    'columns'     => [
        'calendrier_id' => 'id',
    ],
];

//@formatter:on
