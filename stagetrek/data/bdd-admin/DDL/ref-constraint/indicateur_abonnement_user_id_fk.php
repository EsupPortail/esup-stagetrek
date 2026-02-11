<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'indicateur_abonnement_user_id_fk',
    'table'       => 'unicaen_indicateur_abonnement',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'user_pkey',
    'columns'     => [
        'user_id' => 'id',
    ],
];

//@formatter:on
