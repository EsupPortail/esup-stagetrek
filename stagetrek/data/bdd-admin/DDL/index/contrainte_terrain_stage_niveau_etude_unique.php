<?php

//@formatter:off

return [
    'name'    => 'contrainte_terrain_stage_niveau_etude_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'contrainte_terrain_stage_niveau_etude_linker',
    'schema'  => 'public',
    'columns' => [
        'terrain_stage_id',
        'niveau_etude_id',
    ],
];

//@formatter:on
