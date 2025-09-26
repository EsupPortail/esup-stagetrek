<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_evenement_instance_parent_id_fkey',
    'table'       => 'unicaen_evenement_instance',
    'rtable'      => 'unicaen_evenement_instance',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_evenement_instance_pkey',
    'columns'     => [
        'parent_id' => 'id',
    ],
];

//@formatter:on
