<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'groupe_referentiel_promo_linker_groupe_id_fkey',
    'table'       => 'groupe_referentiel_promo_linker',
    'rtable'      => 'groupe',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'groupe_pkey',
    'columns'     => [
        'groupe_id' => 'id',
    ],
];

//@formatter:on
