<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_cursus_categorie_stage_id_fkey',
    'table'       => 'contrainte_cursus',
    'rtable'      => 'categorie_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'categorie_stage_pkey',
    'columns'     => [
        'categorie_stage_id' => 'id',
    ],
];

//@formatter:on
