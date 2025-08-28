<?php

//@formatter:off

return [
    'name'    => 'etudiant_user_id_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'etudiant',
    'schema'  => 'public',
    'columns' => [
        'user_id',
    ],
];

//@formatter:on
