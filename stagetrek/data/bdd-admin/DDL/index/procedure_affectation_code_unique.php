<?php

//@formatter:off

return [
    'name'    => 'procedure_affectation_code_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'procedure_affectation',
    'schema'  => 'public',
    'columns' => [
        'code',
    ],
];

//@formatter:on
