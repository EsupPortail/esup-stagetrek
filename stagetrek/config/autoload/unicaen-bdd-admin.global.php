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
            /* Autres instances éventuelles de votre base de données
            'preprod' => [
                'host'     => 'à renseigner ...',
                ...
            ],
            'prod' => [
                'host'     => 'à renseigner ...',
                ...
            ],
            ...
            */
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

        'data' => [
            'config'  => [
                /* array dont le format est le suivant :
                 *
                 * 'nom_de_ma_table => [
                 *     // à adapter, 'install' par exemple pour un jeu de données servant à installer l'appli qui sera paramétré ensuite
                 *     'actions' => ['install', 'update'],
                 *
                 *     // string|array : liste des colonnes servant à identifier de manière certaine et unique les lignes (par exemple 'code')
                 *     'key' => 'code',
                 *
                 *     // Ce tableau d'options est transmis à la fonction Unicaen\BddAdmin\Table::merge
                 *     // vous pouvez vous renseigner sur l'usage de cette méthode, car bien d'autres possibilités existent...
                 *     'options => [
                 *          // permet au besoin de faire des ajouts et des modifs, mais jamais de supprimer ce ui a été ajouté par ailleurs
                 *          'soft_delete' => false, 'delete' => false,
                 *
                 *          // avec cet exemple la mise à jour du jeu de données ne concernera pas les données saisies dans la colonne ma_col_specifique
                 *          'update-ignore-cols' => ['ma_col_specifique'],
                 *
                 *          ...
                 *     ],
                 * ],
                 * ...
                 */

//                Unicaen_Utilisateur / Priviléges
                'unicaen_utilisateur_role'               => [
                    'actions' => ['install', 'update'],
                    'key'     => 'role_id',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'unicaen_privilege_categorie'               => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => true, 'delete' => true],
                ],
                'unicaen_privilege_privilege'               => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => true, 'delete' => true,
                        'columns' => [ //Permet de faire les jointures des données
                            'categorie_id'      => ['transformer' => 'select id from unicaen_privilege_categorie where code = %s'],
                    ],],
                ],
                'unicaen_privilege_privilege_role_linker' => [
                    'actions'                 => ['install', 'update'],
                    'key'                     => ['role_id', 'privilege_id'],
                    'options'                 => ['update'  => false, 'soft_delete' => true, 'delete' => true,
                        'columns' => [ //Permet de faire les jointures des données
                            'role_id'      => ['transformer' => 'select id from unicaen_utilisateur_role where role_id = %s'],
                            'privilege_id' => ['transformer' => 'select id from unicaen_privilege_privilege where code = %s'],
                        ],
                    ],
                ],
                'unicaen_utilisateur_user' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'id',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
//                UnicaenEtat
                'unicaen_etat_categorie'                    => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'unicaen_etat_type'                         => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update'  => false, 'soft_delete' => false, 'delete' => false,
                        'columns' => ['categorie_id' => ['transformer' => 'select id from unicaen_etat_categorie where code = %s'],
                    ]]
                ],
//                UnicaenEvenements
                'unicaen_evenement_etat'                    => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'unicaen_evenement_type'                    => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
//                Renderer et Macro
                'unicaen_renderer_macro'                    => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'unicaen_renderer_template'                 => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                //Contact
                'contact' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                //Contraitnes de formations
//                TODO : rajouter un champs code
                'contrainte_cursus_portee' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
//                paramètres
                'parametre_categorie' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'parametre_type' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'parametre' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false,
                        'columns' => [
                            'categorie_id' => ['transformer' => 'select id from parametre_categorie where code = %s'],
                            'parametre_type_id' => ['transformer' => 'select id from parametre_type where code = %s'],
                        ],
                    ],
                ],
                'parametre_cout_affectation' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'rang',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                //Algo d'affectation
                'procedure_affectation' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'delete' => true],
                ],

                // Misc
                'adresse_type' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'source' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'faq_categorie_question' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'id',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'fichier_nature' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'niveau_etude' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'id',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
                'terrain_stage_niveau_demande' => [
                    'actions' => ['install', 'update'],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft_delete' => false, 'delete' => false],
                ],
            ],
            'sources' => [
                /* Liste des sources sous forme de tableau
                 * Les valeurs correspondant à des noms de classes PHP qui seront instanciées par le DataManager
                 * Vous pouvez également passer des noms ou alias de services déclarés auprès du serviceManager de Laminas
                 * pour profiter de l'injection de dépendance
                 */
//                Unicaen_Utilisateur / Priviléges
                'unicaen_utilisateur_role' => $bddAdminDir."/donnees/unicaen_utilisateur_role.php",
                'unicaen_privilege_categorie' => $bddAdminDir."/donnees/unicaen_privilege_categorie.php",
                'unicaen_privilege_privilege' => $bddAdminDir."/donnees/unicaen_privilege_privilege.php",
                'unicaen_privilege_privilege_role_linker' => $bddAdminDir."/donnees/unicaen_privilege_privilege_role_linker.php",
                'unicaen_utilisateur_user' => $bddAdminDir."/donnees/unicaen_utilisateur_user.php",
//                UnicaenEtat
                'unicaen_etat_categorie' => $bddAdminDir."/donnees/unicaen_etat_categorie.php",
                'unicaen_etat_type' => $bddAdminDir."/donnees/unicaen_etat_type.php",
//                Unicaen_Evenements
                'unicaen_evenement_etat' => $bddAdminDir."/donnees/unicaen_evenement_etat.php",
                'unicaen_evenement_type' => $bddAdminDir."/donnees/unicaen_evenement_type.php",
//               Renderer
                'unicaen_renderer_macro' => $bddAdminDir."/donnees/unicaen_renderer_macro.php",
                'unicaen_renderer_template' => $bddAdminDir."/donnees/unicaen_renderer_template.php",
//                Contact
                'contact' => $bddAdminDir."/donnees/contact.php",
//                Contraintes de cursus
                'contrainte_cursus_portee' => $bddAdminDir."/donnees/contrainte_cursus_portee.php",
//                Paramètres
                'parametre_categorie' => $bddAdminDir."/donnees/parametre_categorie.php",
                'parametre_type' => $bddAdminDir."/donnees/parametre_type.php",
                'parametre' => $bddAdminDir."/donnees/parametre.php",
                'parametre_cout_affectation' => $bddAdminDir."/donnees/parametre_cout_affectation.php",
//                Procédure d'affectation
                'procedure_affectation'  => $bddAdminDir."/donnees/procedure_affectation.php",
//              Misc
                'adresse_type' => $bddAdminDir."/donnees/adresse_type.php",
                'source' => $bddAdminDir."/donnees/source.php",
                'faq_categorie_question' => $bddAdminDir."/donnees/faq_categorie_question.php",
                'fichier_nature' => $bddAdminDir."/donnees/fichier_nature.php",
                'niveau_etude' => $bddAdminDir."/donnees/niveau_etude.php",
                'terrain_stage_niveau_demande' => $bddAdminDir."/donnees/terrain_stage_niveau_demande.php",
            ],
        ],

        'migration' => [
        ],

        /* Nom des colonnes servant de clé primaire dans vos tables, généralement 'id' pour la compatibilité avec Doctrine
         * Si des tables n'ont pas de colonne 'id' ou personnalisé, le système fonctionnera sans utiliser les séquences pour initialiser la clé primaire
         */
        //'id_column' => 'id',

        'histo' => [
            /* ID par défaut de l'utilisateur utilisé par le DataManager pour insérer ou modifier les données
             * Peut être fourni ici ou bien dans une factory adaptée en utilisant la méthode suivante :
             *
             * $config = [...config de bddAdmin...];
             * $bdd = new Unicaen\BddAdmin\Bdd($config);
             *
             * $monUsername = 'mon_username';
             * $monId = $bdd->selectOne('SELECT id FROM utilisateur WHERE username=:username', ['username' => $monUsername], 'id');
             * $bdd->setHistoUserId($monId);
             *
             * Si user_id est NULL, cette fonctionnalité d'historisation sera désactivée
             */
            'user_id'                      => UserProvider::APP_USER_ID,

            /* Noms des colonnes utilisées pour gérer les historiques
             * Attention : tous les noms doivent être renseignés ou alors tous mis à NULL si pas de gestion d'historiques
             * Se base par défaut sur ce qui est préconisé pour UnicaenApp\Entity\HistoriqueAwareInterface
             *
             * Si vos tables ne possèdent pas l'ensemble de ces colonnes, la gestion de l'historique ne sera pas appliquée sur celles-ci
             */
            //'histo_creation_column'        => 'histo_creation',
            //'histo_modification_column'    => 'histo_modification',
            //'histo_destruction_column'     => 'histo_destruction',
            //'histo_createur_id_column'     => 'histo_createur_id',
            //'histo_modificateur_id_column' => 'histo_modificateur_id',
            //'histo_destructeur_id_column'  => 'histo_destructeur_id',
        ],

        'import' => [
            /* Compatibilité avec un système d'import de données
             * ID de la source par défaut utilisée par le DataManager pour insérer une ligne d'une table synchronisable
             * Peut être fourni ici ou bien dans une factory adaptée en utilisant la méthode suivante :
             *
             * $config = [...config de bddAdmin...];
             * $bdd = new Unicaen\BddAdmin\Bdd($config);
             *
             * $code = 'ma_source';
             * $monId = $bdd->selectOne('SELECT id FROM source WHERE code=:code', ['code' => $code], 'id');
             * $bdd->setSourceId($monId);
             *
             * Si source_id est NULL, cette fonctionnalité d'initialisation de sources sera désactivée
             *
             */
            //'source_id'          => null,

            /* Noms des colonnes utilisées pour gérer les données liées à l'import depuis d'autres logiciels
             * Attention : tous les noms doivent être renseignés ou alors tous mis à NULL si pas de gestion d'import
             *
             * Si vos tables ne possèdent pas l'ensemble de ces colonnes, la gestion des colonnes d'import ne sera pas appliquée sur celles-ci
             */
            //'source_id_column'   => 'source_id',
            //'source_code_column' => 'source_code',
        ],
    ],
];