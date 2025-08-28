<?php

//@formatter:off

return [
    'name'    => 'stage_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'stage',
    'schema'  => 'public',
    'columns' => [
        'id',
        'session_stage_id',
        'etudiant_id',
    ],
];

//@formatter:on
