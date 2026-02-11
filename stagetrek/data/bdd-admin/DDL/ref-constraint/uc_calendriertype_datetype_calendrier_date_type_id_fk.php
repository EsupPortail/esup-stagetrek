<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'uc_calendriertype_datetype_calendrier_date_type_id_fk',
    'table'       => 'unicaen_calendrier_calendriertype_datetype',
    'rtable'      => 'unicaen_calendrier_date_type',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_calendrier_date_type_pk',
    'columns'     => [
        'date_type_id' => 'id',
    ],
];

//@formatter:on
