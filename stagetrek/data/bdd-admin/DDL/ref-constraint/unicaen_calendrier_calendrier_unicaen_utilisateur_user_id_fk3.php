<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_calendrier_calendrier_unicaen_utilisateur_user_id_fk3',
    'table'       => 'unicaen_calendrier_calendrier',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'user_pkey',
    'columns'     => [
        'histo_destructeur_id' => 'id',
    ],
];

//@formatter:on
