<?php

//@formatter:off

return [
    'name'    => 'contrainte_cursus_portee_code_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contrainte_cursus_portee',
    'schema'  => 'public',
    'columns' => [
        'code',
    ],
];

//@formatter:on
