<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'import_observ_result_ioe_fk',
    'table'       => 'import_observ_result',
    'rtable'      => 'import_observ',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'CASCADE',
    'index'       => 'import_observ_pk',
    'columns'     => [
        'import_observ_id' => 'id',
    ],
];

//@formatter:on
