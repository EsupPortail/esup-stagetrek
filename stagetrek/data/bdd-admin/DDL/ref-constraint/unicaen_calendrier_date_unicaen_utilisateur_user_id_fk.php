<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_calendrier_date_unicaen_utilisateur_user_id_fk',
    'table'       => 'unicaen_calendrier_date',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'user_pkey',
    'columns'     => [
        'histo_createur_id' => 'id',
    ],
];

//@formatter:on
