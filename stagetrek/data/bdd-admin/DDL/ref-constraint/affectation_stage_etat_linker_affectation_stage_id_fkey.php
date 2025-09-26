<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'affectation_stage_etat_linker_affectation_stage_id_fkey',
    'table'       => 'affectation_stage_etat_linker',
    'rtable'      => 'affectation_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'affectation_stage_pkey',
    'columns'     => [
        'affectation_stage_id' => 'id',
    ],
];

//@formatter:on
