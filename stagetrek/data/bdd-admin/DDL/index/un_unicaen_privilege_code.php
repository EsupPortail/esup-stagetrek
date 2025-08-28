<?php

//@formatter:off

return [
    'name'    => 'un_unicaen_privilege_code',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'unicaen_privilege_privilege',
    'schema'  => 'public',
    'columns' => [
        'categorie_id',
        'code',
    ],
];

//@formatter:on
