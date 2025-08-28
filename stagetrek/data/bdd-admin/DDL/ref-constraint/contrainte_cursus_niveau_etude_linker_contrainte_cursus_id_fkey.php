<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_cursus_niveau_etude_linker_contrainte_cursus_id_fkey',
    'table'       => 'contrainte_cursus_niveau_etude_linker',
    'rtable'      => 'contrainte_cursus',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'contrainte_cursus_pkey',
    'columns'     => [
        'contrainte_cursus_id' => 'id',
    ],
];

//@formatter:on
