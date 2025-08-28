<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'privilege_categorie_privilege_id_fk',
    'table'       => 'unicaen_privilege_privilege',
    'rtable'      => 'unicaen_privilege_categorie',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'categorie_privilege_pkey',
    'columns'     => [
        'categorie_id' => 'id',
    ],
];

//@formatter:on
