<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_indicateur_tableau_indicateur_indicateur_null_fk',
    'table'       => 'unicaen_indicateur_tableau_indicateur',
    'rtable'      => 'unicaen_indicateur_indicateur',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'indicateur_id_uindex',
    'columns'     => [
        'indicateur_id' => 'id',
    ],
];

//@formatter:on
