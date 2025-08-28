<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'contrainte_terrain_stage_niveau_etude_link_niveau_etude_id_fkey',
    'table'       => 'contrainte_terrain_stage_niveau_etude_linker',
    'rtable'      => 'niveau_etude',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'niveau_etude_pkey',
    'columns'     => [
        'niveau_etude_id' => 'id',
    ],
];

//@formatter:on
