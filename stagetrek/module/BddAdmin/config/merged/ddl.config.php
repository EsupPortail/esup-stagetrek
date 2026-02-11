<?php

namespace BddAdmin;


/**
 * Filtres automatiques pour les schéma de la ddl
 * On exclue automatiquement les vue v_diff issue de db_imports
 * On exclue les vues matérialisé d'indicateur (commencant par mvi_ , choix arbitraire de convention de nommage)
 */
return [
    'unicaen-bddadmin' => [
        'ddl' => [
            'update-ddl-filters'     => [
                'view'               => ['excludes' => ['v_diff%' ]],
                'materialized-view'  => ['excludes' => ['mvind_%' ],],
            ],
        ],
    ],
];