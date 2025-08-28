<?php

//@formatter:off

return [
    'name'    => 'etudiant_groupe_linker_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'etudiant_groupe_linker',
    'schema'  => 'public',
    'columns' => [
        'etudiant_id',
        'groupe_id',
    ],
];

//@formatter:on
