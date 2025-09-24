<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'groupe_referentiel_promo_linker_referentiel_promo_id_fkey',
    'table'       => 'groupe_referentiel_promo_linker',
    'rtable'      => 'referentiel_promo',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'referentiel_promo_pkey',
    'columns'     => [
        'referentiel_promo_id' => 'id',
    ],
];

//@formatter:on
