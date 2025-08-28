<?php

//@formatter:off

return [
    'name'    => 'ix_unicaen_utilisateur_role_parent',
    'unique'  => FALSE,
    'type'    => 'btree',
    'table'   => 'unicaen_utilisateur_role',
    'schema'  => 'public',
    'columns' => [
        'parent_id',
    ],
];

//@formatter:on
