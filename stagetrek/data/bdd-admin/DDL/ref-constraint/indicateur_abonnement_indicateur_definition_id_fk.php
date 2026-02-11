<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'indicateur_abonnement_indicateur_definition_id_fk',
    'table'       => 'unicaen_indicateur_abonnement',
    'rtable'      => 'unicaen_indicateur_indicateur',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'indicateur_id_uindex',
    'columns'     => [
        'indicateur_id' => 'id',
    ],
];

//@formatter:on
