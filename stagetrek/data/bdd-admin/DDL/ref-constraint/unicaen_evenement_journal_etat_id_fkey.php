<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_evenement_journal_etat_id_fkey',
    'table'       => 'unicaen_evenement_journal',
    'rtable'      => 'unicaen_evenement_etat',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'unicaen_evenement_etat_pk',
    'columns'     => [
        'etat_id' => 'id',
    ],
];

//@formatter:on
