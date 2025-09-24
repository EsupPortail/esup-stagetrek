<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'stage_etudiant_id_fkey',
    'table'       => 'stage',
    'rtable'      => 'etudiant',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'etudiant_pkey',
    'columns'     => [
        'etudiant_id' => 'id',
    ],
];

//@formatter:on
