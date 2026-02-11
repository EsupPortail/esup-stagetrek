<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'uc_calendriertype_calendrier_type_id_fk',
    'table'       => 'unicaen_calendrier_calendrier',
    'rtable'      => 'unicaen_calendrier_calendrier_type',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_calendrier_calendrier_type_pk',
    'columns'     => [
        'calendrier_type_id' => 'id',
    ],
];

//@formatter:on
