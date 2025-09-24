<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_etat_instance_type_id',
    'table'       => 'unicaen_etat_instance',
    'rtable'      => 'unicaen_etat_type',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_etat_type_id_uindex',
    'columns'     => [
        'type_id' => 'id',
    ],
];

//@formatter:on
