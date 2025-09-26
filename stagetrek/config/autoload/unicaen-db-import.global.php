<?php
/**
 * Configuration pour l'import des étudiants depuis une source externe
 * (repose en grande partie sur unicaen/db-import)
 */

use Application\Entity\Db\Source;
use Application\Provider\Roles\UserProvider;

return [
    'import' => [
//            // Gestions des sources
        'source_entity_class' => Source::class,
        'default_source_code' => Source::STAGETREK, //On force ici la source comme étant Stagetrek a fin de permetre la fusion des différentes sources

        //Config des champs histo
        'histo_columns_aliases' => [
            'created_on' => 'histo_creation',
            'updated_on' => 'histo_modification',
            'deleted_on' => 'histo_destruction',
            'created_by' => 'histo_createur_id',
            'updated_by' => 'histo_modificateur_id',
            'deleted_by' => 'histo_destructeur_id',
        ],
        'histo_columns_values' => [
            'created_by' => UserProvider::APP_USER_ID,
            'updated_by' => UserProvider::APP_USER_ID,
            'deleted_by' => UserProvider::APP_USER_ID,
        ],
//
        // pas d'observateur pour les sauvegarde
        'use_import_observ' => false,
    ]
];