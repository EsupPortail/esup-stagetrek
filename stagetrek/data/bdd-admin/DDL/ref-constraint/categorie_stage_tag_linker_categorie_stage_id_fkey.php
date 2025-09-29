<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'categorie_stage_tag_linker_categorie_stage_id_fkey',
    'table'       => 'categorie_stage_tag_linker',
    'rtable'      => 'categorie_stage',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'categorie_stage_pkey',
    'columns'     => [
        'categorie_stage_id' => 'id',
    ],
];

//@formatter:on
