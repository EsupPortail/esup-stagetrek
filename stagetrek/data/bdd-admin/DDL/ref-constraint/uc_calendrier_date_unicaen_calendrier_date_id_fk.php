<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'uc_calendrier_date_unicaen_calendrier_date_id_fk',
    'table'       => 'unicaen_calendrier_calendrier_date',
    'rtable'      => 'unicaen_calendrier_date',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_calendrier_date_pk',
    'columns'     => [
        'date_id' => 'id',
    ],
];

//@formatter:on
