<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'preference_histo_createur_id_fkey',
    'table'       => 'preference',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'user_pkey',
    'columns'     => [
        'histo_createur_id' => 'id',
    ],
];

//@formatter:on
