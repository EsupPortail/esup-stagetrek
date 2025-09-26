<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_cursus_portee_fkey',
    'table'       => 'contrainte_cursus',
    'rtable'      => 'contrainte_cursus_portee',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contrainte_cursus_portee_pkey',
    'columns'     => [
        'portee' => 'id',
    ],
];

//@formatter:on
