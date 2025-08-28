<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'convention_stage_modele_convention_stage_id_fkey',
    'table'       => 'convention_stage',
    'rtable'      => 'modele_convention_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'modele_convention_stage_pkey',
    'columns'     => [
        'modele_convention_stage_id' => 'id',
    ],
];

//@formatter:on
