<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'fk_unicaen_tag_categorie',
    'table'       => 'unicaen_tag',
    'rtable'      => 'unicaen_tag_categorie',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_tag_categorie_pkey',
    'columns'     => [
        'categorie_id' => 'id',
    ],
];

//@formatter:on
