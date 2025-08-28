<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'groupe_niveau_etude_id_fkey',
    'table'       => 'groupe',
    'rtable'      => 'niveau_etude',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'niveau_etude_pkey',
    'columns'     => [
        'niveau_etude_id' => 'id',
    ],
];

//@formatter:on
