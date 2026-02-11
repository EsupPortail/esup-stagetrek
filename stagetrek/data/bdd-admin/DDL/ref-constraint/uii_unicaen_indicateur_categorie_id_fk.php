<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'uii_unicaen_indicateur_categorie_id_fk',
    'table'       => 'unicaen_indicateur_indicateur',
    'rtable'      => 'unicaen_indicateur_categorie',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_indicateur_categorie_pk',
    'columns'     => [
        'categorie_id' => 'id',
    ],
];

//@formatter:on
