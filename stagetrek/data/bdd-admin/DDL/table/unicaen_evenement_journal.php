<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_evenement_journal',
    'temporary'   => FALSE,
    'logging'     => TRUE,
    'commentaire' => NULL,
    'sequence'    => 'unicaen_evenement_journal_id_seq',
    'columns'     => [
        'date_execution' => [
            'name'        => 'date_execution',
            'type'        => 'date',
            'bdd-type'    => 'timestamp without time zone',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 6,
            'nullable'    => FALSE,
            'default'     => NULL,
            'position'    => 2,
            'commentaire' => NULL,
        ],
        'etat_id'        => [
            'name'        => 'etat_id',
            'type'        => 'int',
            'bdd-type'    => 'bigint',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 8,
            'nullable'    => FALSE,
            'default'     => NULL,
            'position'    => 4,
            'commentaire' => NULL,
        ],
        'id'             => [
            'name'        => 'id',
            'type'        => 'int',
            'bdd-type'    => 'integer',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => 4,
            'nullable'    => FALSE,
            'default'     => 'nextval(\'unicaen_evenement_journal_id_seq\'::regclass)',
            'position'    => 1,
            'commentaire' => NULL,
        ],
        'log'            => [
            'name'        => 'log',
            'type'        => 'clob',
            'bdd-type'    => 'text',
            'length'      => 0,
            'scale'       => NULL,
            'precision'   => NULL,
            'nullable'    => TRUE,
            'default'     => NULL,
            'position'    => 3,
            'commentaire' => NULL,
        ],
    ],
];

//@formatter:on
