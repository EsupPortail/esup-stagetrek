<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'stage_etat_linker_etat_instance_id_fkey',
    'table'       => 'stage_etat_linker',
    'rtable'      => 'unicaen_etat_instance',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_etat_instance_pk',
    'columns'     => [
        'etat_instance_id' => 'id',
    ],
];

//@formatter:on
