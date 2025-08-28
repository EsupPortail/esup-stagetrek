<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'niveau_etude_niveau_etude_parent_fkey',
    'table'       => 'niveau_etude',
    'rtable'      => 'niveau_etude',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'niveau_etude_pkey',
    'columns'     => [
        'niveau_etude_parent' => 'id',
    ],
];

//@formatter:on
