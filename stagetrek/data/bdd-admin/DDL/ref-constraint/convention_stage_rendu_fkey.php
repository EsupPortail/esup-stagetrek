<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'convention_stage_rendu_fkey',
    'table'       => 'convention_stage',
    'rtable'      => 'unicaen_renderer_rendu',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_renderer_rendu_pkey',
    'columns'     => [
        'rendu' => 'id',
    ],
];

//@formatter:on
