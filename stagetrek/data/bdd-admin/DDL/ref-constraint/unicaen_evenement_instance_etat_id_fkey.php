<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_evenement_instance_etat_id_fkey',
    'table'       => 'unicaen_evenement_instance',
    'rtable'      => 'unicaen_evenement_etat',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_evenement_etat_pk',
    'columns'     => [
        'etat_id' => 'id',
    ],
];

//@formatter:on
