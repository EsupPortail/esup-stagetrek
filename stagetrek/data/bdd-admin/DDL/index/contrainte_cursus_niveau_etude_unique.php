<?php

//@formatter:off

return [
    'name'    => 'contrainte_cursus_niveau_etude_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contrainte_cursus_niveau_etude_linker',
    'schema'  => 'public',
    'columns' => [
        'contrainte_cursus_id',
        'niveau_etude_id',
    ],
];

//@formatter:on
