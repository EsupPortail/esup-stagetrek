<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_etat_type_categorie_id_fk',
    'table'       => 'unicaen_etat_type',
    'rtable'      => 'unicaen_etat_categorie',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_etat_categorie_pk',
    'columns'     => [
        'categorie_id' => 'id',
    ],
];

//@formatter:on
