<?php

//@formatter:off

return [
    'name'    => 'ix_unicaen_utilisateur_user_last_role',
    'unique'  => FALSE,
    'type'    => 'btree',
    'table'   => 'unicaen_utilisateur_user',
    'schema'  => 'public',
    'columns' => [
        'last_role_id',
    ],
];

//@formatter:on
