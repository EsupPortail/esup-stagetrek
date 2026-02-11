<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_indicateur_tableau_indicateur_tableaudebord_null_fk',
    'table'       => 'unicaen_indicateur_tableau_indicateur',
    'rtable'      => 'unicaen_indicateur_tableaudebord',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'unicaen_indicateur_tableaudebord_pk',
    'columns'     => [
        'tableau_id' => 'id',
    ],
];

//@formatter:on
