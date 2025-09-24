<?php

//@formatter:off

return [
    'name'    => 'contrainte_cursus_etudiant_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contrainte_cursus_etudiant',
    'schema'  => 'public',
    'columns' => [
        'etudiant_id',
        'contrainte_id',
    ],
];

//@formatter:on
