<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'uc_calendrier_date_unicaen_calendrier_calendrier_id_fk',
    'table'       => 'unicaen_calendrier_calendrier_date',
    'rtable'      => 'unicaen_calendrier_calendrier',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_calendrier_calendrier_pk',
    'columns'     => [
        'calendrier_id' => 'id',
    ],
];

//@formatter:on
