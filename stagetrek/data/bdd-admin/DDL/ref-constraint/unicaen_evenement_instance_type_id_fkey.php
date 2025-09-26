<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_evenement_instance_type_id_fkey',
    'table'       => 'unicaen_evenement_instance',
    'rtable'      => 'unicaen_evenement_type',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_evenement_type_pkey',
    'columns'     => [
        'type_id' => 'id',
    ],
];

//@formatter:on
