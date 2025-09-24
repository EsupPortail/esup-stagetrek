<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'parametre_cout_affectation',
    'temporary'   => FALSE,
    'logging'     => TRUE,
    'commentaire' => NULL,
    'sequence'    => 'parametre_cout_affectation_id_seq',
    'columns'     => [
        'cout' => [
            'name'        => 'cout',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => FALSE,
            'default'     => '0',
            'position'    => 3,
            'commentaire' => NULL,
        ],
        'id'   => [
            'name'        => 'id',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => FALSE,
            'default'     => 'nextval(\'parametre_cout_affectation_id_seq\'::regclass)',
            'position'    => 1,
            'commentaire' => NULL,
        ],
        'rang' => [
            'name'        => 'rang',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => FALSE,
            'default'     => '1',
            'position'    => 2,
            'commentaire' => NULL,
        ],
    ],
];

//@formatter:on
