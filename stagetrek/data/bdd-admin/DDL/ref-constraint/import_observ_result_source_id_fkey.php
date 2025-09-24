<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'import_observ_result_source_id_fkey',
    'table'       => 'import_observ_result',
    'rtable'      => 'source',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'source_pkey',
    'columns'     => [
        'source_id' => 'id',
    ],
];

//@formatter:on
