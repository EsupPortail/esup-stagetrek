<?php

/*
 * Fichier à copier/coller dans config/autoload/unicaen-bddadmin.global.php
 *
 * Les options commentées ici sont placées pour illustrer les valeurs par défaut
 * A décommenter et modifier le cas échéant
 */

use Application\Provider\Roles\UserProvider;
$bddAdminDir = ($_ENV['BDD_ADMIN_DIR']) ?? "data/bdd-admin";
return [
    'unicaen-bddadmin' => [
        'connection' => [
            'default' => [ // généralement : base de dév, peut s'appeler autrement que 'default'
                'driver' => 'Postgresql',
                'port'   => ($_ENV['DATABASE_PORT']) ?? "Non-configuré",
                'host'   => ($_ENV['DATABASE_HOST']) ?? "Non-configuré",

                'dbname'   => ($_ENV['DATABASE_NAME']) ?? "Non-configuré",
                'username' => ($_ENV['DATABASE_USER']) ?? "Non-configuré",
                'password' => ($_ENV['DATABASE_PSWD']) ?? "Non-configuré",
            ],
        ],

        /* Connexion à utiliser par défaut, nom à sélectionner parmi la liste des connexions disponibles */
        'current_connection' => 'default',


        'ddl' => [
            //TODO : en var d'environnement : uniquement le BDD_ADMIN_DIR et imposé les repertoire/nom des fichier ...
            /* Répertoire où placer votre DDL */
            'dir'                    => $bddAdminDir."/ddl",

            /* Nom par défaut du fichier de sauvegarde des positionnements de colonnes */
            'columns_positions_file' => $bddAdminDir."/ddl_columns_pos.php",

            /* array général des filtres de DDL à appliquer afin d'ignorer ou bien de forcer la prise en compte de certains objets lors de la mise à jour
             * le format d'array doit respecter la spécification des DdlFilters
             * Ce tableau des filtres est utilisé aussi bien en MAJ DDL qu'en MAJ BDD
             */
            'filters'                => [],

            /* array des filtres dédié à la mise à jour de la base de données à partir de la DDL
             * le format d'array doit respecter la spécification des DdlFilters
             * pour les update-bdd, le mode EXPLICIT est forcé : c'est à dire que ce qui n'est pas spécifié dans le filtre n'existe pas
             * le filtre est initialisé avec les objets déjà présents en DDL
             */
            'update-bdd-filters'     => [],

            /* array des filtres dédié à la mise à jour de la DDL, afin d'éviter que ne se retrouvent en DDL certains objets présents en base
             * le format d'array doit respecter la spécification des DdlFilters
             */
            'update-ddl-filters'     => [],
        ],

        /* Nom des colonnes servant de clé primaire dans vos tables, généralement 'id' pour la compatibilité avec Doctrine
         * Si des tables n'ont pas de colonne 'id' ou personnalisé, le système fonctionnera sans utiliser les séquences pour initialiser la clé primaire
         */
        //'id_column' => 'id',
        'histo' => [
            'user_id'                      => UserProvider::APP_USER_ID,
        ],
    ],
];