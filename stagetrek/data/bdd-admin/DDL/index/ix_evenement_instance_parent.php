<?php

//@formatter:off

return [
    'name'    => 'ix_evenement_instance_parent',
    'unique'  => FALSE,
    'type'    => 'btree',
    'table'   => 'unicaen_evenement_instance',
    'schema'  => 'public',
    'columns' => [
        'parent_id',
    ],
];

//@formatter:on
