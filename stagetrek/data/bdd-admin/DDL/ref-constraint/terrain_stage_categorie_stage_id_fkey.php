<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'terrain_stage_categorie_stage_id_fkey',
    'table'       => 'terrain_stage',
    'rtable'      => 'categorie_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'categorie_stage_pkey',
    'columns'     => [
        'categorie_stage_id' => 'id',
    ],
];

//@formatter:on
