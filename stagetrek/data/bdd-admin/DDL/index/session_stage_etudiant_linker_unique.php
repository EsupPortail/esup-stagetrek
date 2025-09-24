<?php

//@formatter:off

return [
    'name'    => 'session_stage_etudiant_linker_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'session_stage_etudiant_linker',
    'schema'  => 'public',
    'columns' => [
        'session_stage_id',
        'etudiant_id',
    ],
];

//@formatter:on
