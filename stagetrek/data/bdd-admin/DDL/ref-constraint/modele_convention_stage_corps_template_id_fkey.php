<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'modele_convention_stage_corps_template_id_fkey',
    'table'       => 'modele_convention_stage',
    'rtable'      => 'unicaen_renderer_template',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'unicaen_document_template_id_uindex',
    'columns'     => [
        'corps_template_id' => 'id',
    ],
];

//@formatter:on
