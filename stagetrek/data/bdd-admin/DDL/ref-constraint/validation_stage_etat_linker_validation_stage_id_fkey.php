<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'validation_stage_etat_linker_validation_stage_id_fkey',
    'table'       => 'validation_stage_etat_linker',
    'rtable'      => 'validation_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'validation_stage_pkey',
    'columns'     => [
        'validation_stage_id' => 'id',
    ],
];

//@formatter:on
