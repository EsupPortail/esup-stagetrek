<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'referentiel_promo_source_id_fkey',
    'table'       => 'referentiel_promo',
    'rtable'      => 'source',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'source_pkey',
    'columns'     => [
        'source_id' => 'id',
    ],
];

//@formatter:on
