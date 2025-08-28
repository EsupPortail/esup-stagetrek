<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'unicaen_renderer_rendu_template_id_fkey',
    'table'       => 'unicaen_renderer_rendu',
    'rtable'      => 'unicaen_renderer_template',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'SET NULL',
    'index'       => 'unicaen_document_template_id_uindex',
    'columns'     => [
        'template_id' => 'id',
    ],
];

//@formatter:on
