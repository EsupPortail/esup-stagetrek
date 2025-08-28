<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'modele_convention_stage_histo_modificateur_id_fkey',
    'table'       => 'modele_convention_stage',
    'rtable'      => 'unicaen_utilisateur_user',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'user_pkey',
    'columns'     => [
        'histo_modificateur_id' => 'id',
    ],
];

//@formatter:on
