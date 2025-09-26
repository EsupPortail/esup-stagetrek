<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'parametre_terrain_cout_affectation_fixe',
    'temporary'   => FALSE,
    'logging'     => TRUE,
    'commentaire' => NULL,
    'sequence'    => 'parametre_terrain_cout_affectation_fixe_id_seq',
    'columns'     => [
        'cout'             => [
            'name'        => 'cout',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => TRUE,
            'default'     => '0',
            'position'    => 3,
            'commentaire' => NULL,
        ],
        'id'               => [
            'name'        => 'id',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => FALSE,
            'default'     => 'nextval(\'parametre_terrain_cout_affectation_fixe_id_seq\'::regclass)',
            'position'    => 1,
            'commentaire' => NULL,
        ],
        'terrain_stage_id' => [
            'name'        => 'terrain_stage_id',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => FALSE,
            'default'     => NULL,
            'position'    => 2,
            'commentaire' => NULL,
        ],
        'use_cout_median'  => [
            'name'        => 'use_cout_median',
            'type'        => 'bool',
            'bdd-type'    => 'boolean',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => NULL,
            'nullable'    => FALSE,
            'default'     => 'false',
            'position'    => 4,
            'commentaire' => NULL,
        ],
    ],
];

//@formatter:on
