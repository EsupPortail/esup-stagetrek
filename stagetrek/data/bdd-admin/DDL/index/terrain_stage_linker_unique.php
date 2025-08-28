<?php

//@formatter:off

return [
    'name'    => 'terrain_stage_linker_unique',
    'unique'  => TRUE,
    'type'    => 'btree',
    'table'   => 'terrain_stage_linker',
    'schema'  => 'public',
    'columns' => [
        'terrain_principal_id',
        'terrain_secondaire_id',
    ],
];

//@formatter:on
