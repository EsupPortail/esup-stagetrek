<?php

//@formatter:off

return [
    'schema'      => 'public',
    'name'        => 'faq_faq_categorie_id_fkey',
    'table'       => 'faq',
    'rtable'      => 'faq_categorie_question',
    'update_rule' => 'NO ACTION',
    'delete_rule' => 'NO ACTION',
    'index'       => 'faq_categorie_question_pkey',
    'columns'     => [
        'faq_categorie_id' => 'id',
    ],
];

//@formatter:on
