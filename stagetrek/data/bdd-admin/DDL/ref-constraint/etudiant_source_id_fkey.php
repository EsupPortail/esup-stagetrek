<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'etudiant_source_id_fkey',
    'table'       => 'etudiant',
    'rtable'      => 'source',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'source_pkey',
    'columns'     => [
        'source_id' => 'id',
    ],
];

//@formatter:on
