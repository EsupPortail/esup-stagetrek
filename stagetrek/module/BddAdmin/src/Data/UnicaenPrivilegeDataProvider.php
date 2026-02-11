<?php

namespace BddAdmin\Data;

use Application\Misc\Util;
use Application\Provider\Roles\RolesProvider;

use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenPrivilegeDataProvider implements DataProviderInterface {

    static public function getConfig(string $table, array $config = []): array
    {

        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_privilege_categorie' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
                ];
                break;
            case 'unicaen_privilege_privilege' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => ['categorie_id', 'code'],
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false,
                        'columns' => [
                            'categorie_id'      => ['transformer' => 'select id from unicaen_privilege_categorie where code = %s'],
                        ],],
                ];
                break;
            case 'unicaen_privilege_privilege_role_linker' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'                     => ['role_id', 'privilege_id'],
                    'options'                 => ['update'  => false, 'soft-delete' => false, 'delete' => false,
                        'columns' => [
                            'role_id'      => ['transformer' => 'select id from unicaen_utilisateur_role where role_id = %s'],
                            'privilege_id' => ['transformer' => 'select id from unicaen_privilege_privilege where code = %s'],
                        ],
                    ],
                ];
                break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_privilege_categorie(): array
    {
        $ordre = 1;
        return [
            [
                "code" => "etudiant",
                "libelle" => "Gestion des étudiants",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "terrain",
                "libelle" => "Gestion des terrains",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee",
                "libelle" => "Gestion des années universitaires",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "session",
                "libelle" => "Gestion des sessions de stages",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage",
                "libelle" => "Gestion des stages",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "contact",
                "libelle" => "Gestion des contacts de stages",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "convention",
                "libelle" => "Gestion des conventions de stage",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "utilisateur",
                "libelle" => "Gestion des utilisateurs",
                "namespace" => "UnicaenUtilisateur\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "role",
                "libelle" => "Gestion des rôles",
                "namespace" => "UnicaenUtilisateur\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "privilege",
                "libelle" => "Gestion des priviléges",
                "namespace" => "UnicaenPrivilige\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "message",
                "libelle" => "Gestion des messages",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "faq",
                "libelle" => "Gestion de la FAQ",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "referentiel",
                "libelle" => "Gestion des référentiel de données",
                "namespace" => "Referentiel\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "unicaen-db-import",
                "libelle" => "Import",
                "namespace" => "UnicaenDbImport\Provider\Privilege",
                "ordre" => ++$ordre
            ],
            [
                "code" => "parametre",
                "libelle" => "Gestion des paramètres",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "evenementetat",
                "libelle" => "Gestion des états d'événements",
                "namespace" => "UnicaenEvenement\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "evenementtype",
                "libelle" => "Gestion des types d'événements",
                "namespace" => "UnicaenEvenement\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "evenementinstance",
                "libelle" => "Gestion des événements",
                "namespace" => "UnicaenEvenement\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "documentmacro",
                "libelle" => "UnicaenRenderer - Gestion des macros",
                "namespace" => "UnicaenRenderer\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "documenttemplate",
                "libelle" => "UnicaenRenderer - Gestion des templates",
                "namespace" => "UnicaenRenderer\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "documentcontenu",
                "libelle" => "UnicaenRenderer - Gestion des rendus",
                "namespace" => "UnicaenRenderer\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "mail",
                "libelle" => "UnicaenMail - Gestion des mails",
                "namespace" => "UnicaenMail\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "etat",
                "libelle" => "Gestion des états",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "tag",
                "libelle" => "Gestion des tags",
                "namespace" => "Application\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "storage",
                "libelle" => "Stockage de fichiers",
                "namespace" => "UnicaenStorage\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "fichier",
                "libelle" => "Gesion de fichiers",
                "namespace" => "Fichier\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "indicateur",
                "libelle" => "Gestions des indicateurs",
                "namespace" => "UnicaenIndicateur\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "abonnement",
                "libelle" => "Gestions des abonnement",
                "namespace" => "UnicaenIndicateur\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "tableaudebord",
                "libelle" => "Gestions des tableaux de bord",
                "namespace" => "UnicaenIndicateur\Provider\Privilege",
                "ordre" => $ordre++,
            ],
            [
                "code" => "unicaencalendrier_index",
                "libelle" => "Paramétrage du module de gestion des calendrier",
                "namespace" => "UnicaenCalendrier\Provider\Privilege",
                "ordre" => ++$ordre
            ],
            [
                "code" => "calendriertype",
                "libelle" => "Gestion des types de calendriers",
                "namespace" => "UnicaenCalendrier\Provider\Privilege",
                "ordre" => ++$ordre
            ],
            [
                "code" => "calendrier",
                "libelle" => "Gestion des calendriers",
                "namespace" => "UnicaenCalendrier\Provider\Privilege",
                "ordre" => ++$ordre
            ],
            [
                "code" => "datetype",
                "libelle" => "Gestion des types de dates",
                "namespace" => "UnicaenCalendrier\Provider\Privilege",
                "ordre" => ++$ordre
            ],
            [
                "code" => "date",
                "libelle" => "Gestion des dates",
                "namespace" => "UnicaenCalendrier\Provider\Privilege",
                "ordre" => ++$ordre
            ],
        ];
    }

    public function unicaen_privilege_privilege(): array
    {
        $ordre = 1;
        $res = [
            /** Etudiant */
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_afficher",
                "libelle" => "Afficher les étudiant".Util::POINT_MEDIANT."s",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_ajouter",
                "libelle" => "Ajouter des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_modifier",
                "libelle" => "Modifier les étudiant".Util::POINT_MEDIANT."s",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_supprimer",
                "libelle" => "Supprimer des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_own_profil_afficher",
                "libelle" => "Afficher son profil",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "groupe_afficher",
                "libelle" => "Afficher les groupes d'étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "groupe_ajouter",
                "libelle" => "Ajouter des groupes d'étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "groupe_modifier",
                "libelle" => "Modifier les groupes d'étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "groupe_supprimer",
                "libelle" => "Supprimer des groupes d'étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "groupe_administrer_etudiants",
                "libelle" => "Administrer les étudiant".Util::POINT_MEDIANT."s inscrit dans un groupe",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "preference_afficher",
                "libelle" => "Afficher les préférences des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "preference_ajouter",
                "libelle" => "Ajouter des préférences à un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "preference_modifier",
                "libelle" => "Modifer les préférences d'un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "preference_supprimer",
                "libelle" => "Supprimer les préférences d'un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_own_preferences_afficher",
                "libelle" => "Afficher ses préférences de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_own_preferences_ajouter",
                "libelle" => "Ajouter ses préférences de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_own_preferences_modifier",
                "libelle" => "Modifier ses préférences de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_own_preferences_supprimer",
                "libelle" => "Supprimer ses préférences de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "disponibilite_afficher",
                "libelle" => "Afficher les disponiblité des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "disponibilite_ajouter",
                "libelle" => "Ajouter des disponiblité à un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "disponibilite_modifier",
                "libelle" => "Modifer les disponiblité d'un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "disponibilite_supprimer",
                "libelle" => "Supprimer les disponiblité d'un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_contraintes_afficher",
                "libelle" => "Afficher les contraintes du curusus d'un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_contrainte_modifier",
                "libelle" => "Modifier une contrainte pour un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_contrainte_valider",
                "libelle" => "Valider une contrainte pour un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_contrainte_invalider",
                "libelle" => "Invalider une contrainte pour un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_contrainte_activer",
                "libelle" => "Activer une contrainte pour un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etudiant",
                "code" => "etudiant_contrainte_desactiver",
                "libelle" => "Désactiver une contrainte pour un étudiant",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "categorie_stage_afficher",
                "libelle" => "Afficher les catégories de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "categorie_stage_ajouter",
                "libelle" => "Ajouter des catégories des stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "categorie_stage_modifier",
                "libelle" => "Modifier les catégories de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "categorie_stage_supprimer",
                "libelle" => "Supprimer des catégories de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "terrain_stage_afficher",
                "libelle" => "Afficher les terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "terrain_stage_ajouter",
                "libelle" => "Ajouter des terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "terrain_stage_modifier",
                "libelle" => "Modifier les terrains de stages ",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "terrain_stage_supprimer",
                "libelle" => "Supprimer des terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "terrains_importer",
                "libelle" => "Importer des données liées aux terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "terrain",
                "code" => "terrains_exporter",
                "libelle" => "Exporter les données liées aux terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "annee",
                "code" => "annee_universitaire_afficher",
                "libelle" => "Afficher les années universitaires",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "annee",
                "code" => "annee_universitaire_ajouter",
                "libelle" => "Ajouter une année universitaire",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "annee",
                "code" => "annee_universitaire_modifier",
                "libelle" => "Modifier les années universitaires",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "annee",
                "code" => "annee_universitaire_supprimer",
                "libelle" => "Supprimer des années universitaires",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "annee",
                "code" => "annee_universitaire_valider",
                "libelle" => "Valider une année universitaire",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "annee",
                "code" => "annee_universitaire_deverrouiller",
                "libelle" => "Déverrouiller une année universitaire",
                "ordre" => $ordre++,
            ],

            /** Sessions */
            [
                "categorie_id" => "session",
                "code" => "session_stage_afficher",
                "libelle" => "Afficher les sessions de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "session",
                "code" => "session_stage_ajouter",
                "libelle" => "Ajouter des sessions de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "session",
                "code" => "session_stage_modifier",
                "libelle" => "Modifier les sessions de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "session",
                "code" => "session_stage_supprimer",
                "libelle" => "Supprimer des sessions de stages",
                "ordre" => $ordre++,
            ],

            /** Stages */
            [
                "categorie_id" => "stage",
                "code" => "stage_afficher",
                "libelle" => "Afficher un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "stage_ajouter",
                "libelle" => "Ajouter un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "stage_modifier",
                "libelle" => "Modifier un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "stage_supprimer",
                "libelle" => "Supprimer un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "etudiant_own_stages_afficher",
                "libelle" => "Afficher ses sessions de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "affectation_afficher",
                "libelle" => "Afficher les affectations de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "affectation_ajouter",
                "libelle" => "Ajouter une affectation de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "affectation_modifier",
                "libelle" => "Modifier une affectation de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "affectation_supprimer",
                "libelle" => "Supprimer une affectation de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "affectation_run_procedure",
                "libelle" => "Appliquer les procédures d'affectation de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "affectation_pre_valider",
                "libelle" => "Pre-valider une affectation de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "commission_valider_affectations",
                "libelle" => "Validation des affectations de stages par la commission",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "validation_stage_afficher",
                "libelle" => "Afficher la validation d'un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "validation_stage_modifier",
                "libelle" => "Modifier l'état de validation d'un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "procedure_afficher",
                "libelle" => "Afficher les procédures d'affectation de stage ",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "stage",
                "code" => "procedure_modifier",
                "libelle" => "Modifier la description des procédures d'affectation de stage ",
                "ordre" => $ordre++,
            ],

            /** Contacts */
            [
                "categorie_id" => "contact",
                "code" => "contact_afficher",
                "libelle" => "Afficher les contacts de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_ajouter",
                "libelle" => "Ajouter un contact",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_modifier",
                "libelle" => "Modifier le contact",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_supprimer",
                "libelle" => "Supprimer un contact",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_importer",
                "libelle" => "Importer des contacts de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_exporter",
                "libelle" => "Exporter les contacts de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_stage_afficher",
                "libelle" => "Afficher les contacts d'un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_stage_ajouter",
                "libelle" => "Ajouter un contact à un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_stage_modifier",
                "libelle" => "Modifier le contact d'un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_stage_supprimer",
                "libelle" => "Supprimer un contact d'un stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_terrain_afficher",
                "libelle" => "Afficher les contacts d'un terrain",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_terrain_ajouter",
                "libelle" => "Ajouter un contact à un terrain",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_terrain_modifier",
                "libelle" => "Modifier le contact d'un terrain",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_terrain_supprimer",
                "libelle" => "Supprimer un contact d'un terrain",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_terrain_importer",
                "libelle" => "Importer des liens entre contacts et terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "contact_terrain_exporter",
                "libelle" => "Exporter les contacts liées aux terrains de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "contact",
                "code" => "send_mail_validation",
                "libelle" => "Envoyer le mail de demande validation",
                "ordre" => $ordre++,
            ],

            [
                "categorie_id" => "convention",
                "code" => "convention_afficher",
                "libelle" => "Afficher les conventions de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "convention_televerser",
                "libelle" => "Téléverser une convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "convention_generer",
                "libelle" => "Générer une convention de stage à partir d'un modéle",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "convention_modifier",
                "libelle" => "Modifier le contenue d'une convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "convention_supprimer",
                "libelle" => "Supprimer une convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "convention_telecharger",
                "libelle" => "Télécharger une convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "modele_convention_afficher",
                "libelle" => "Afficher les modéles de conventions de stages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "modele_convention_ajouter",
                "libelle" => "Ajouter un modéle de convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "modele_convention_modifier",
                "libelle" => "Modifier un modéle de convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "convention",
                "code" => "modele_convention_supprimer",
                "libelle" => "Supprimer un modéle de convention de stage",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "utilisateur",
                "code" => "utilisateur_afficher",
                "libelle" => "Consulter un utilisateur",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "utilisateur",
                "code" => "utilisateur_ajouter",
                "libelle" => "Ajouter un utilisateur",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "utilisateur",
                "code" => "utilisateur_changerstatus",
                "libelle" => "Changer le statut d'un utilisateur",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "utilisateur",
                "code" => "utilisateur_modifierrole",
                "libelle" => "Modifier les rôles attribués à un utilisateur",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "utilisateur",
                "code" => "utilisateur_rechercher",
                "libelle" => "Recherche d'un utilisateur",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "role",
                "code" => "role_afficher",
                "libelle" => "Consulter les rôles",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "role",
                "code" => "role_modifier",
                "libelle" => "Modifier un rôle",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "role",
                "code" => "role_effacer",
                "libelle" => "Supprimer un rôle",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "privilege",
                "code" => "privilege_voir",
                "libelle" => "Afficher les privilèges",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "privilege",
                "code" => "privilege_ajouter",
                "libelle" => "Ajouter un privilège",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "privilege",
                "code" => "privilege_modifier",
                "libelle" => "Modifier un privilège",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "privilege",
                "code" => "privilege_supprimer",
                "libelle" => "Supprimer un privilège",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "privilege",
                "code" => "privilege_affecter",
                "libelle" => "Attribuer un privilège",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "message",
                "code" => "message_info_afficher",
                "libelle" => "Afficher les messages d'informations",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "message",
                "code" => "message_info_ajouter",
                "libelle" => "Ajouter un message d'information",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "message",
                "code" => "message_info_modifier",
                "libelle" => "Modifier un message d'information",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "message",
                "code" => "message_info_supprimer",
                "libelle" => "Supprimer un message d'information",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_question_afficher",
                "libelle" => "Afficher les questions de la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_question_ajouter",
                "libelle" => "Ajouter une question à la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_question_modifier",
                "libelle" => "Modifier une question de la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_question_supprimer",
                "libelle" => "Supprimer une question de la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_categorie_afficher",
                "libelle" => "Afficher les catégories de la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_categorie_ajouter",
                "libelle" => "Ajouter une catégorie à la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_categorie_modifier",
                "libelle" => "Modifier une catégorie de la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "faq",
                "code" => "faq_categorie_supprimer",
                "libelle" => "Supprimer une catégorie de la faq",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "source_afficher",
                "libelle" => "Afficher les sources",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "source_ajouter",
                "libelle" => "Ajouter des sources",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "source_modifier",
                "libelle" => "Modifier les sources",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "source_supprimer",
                "libelle" => "Supprimer des sources",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "promo_afficher",
                "libelle" => "Afficher les codes de promotions d'étudiants dans les référentiels",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "promo_ajouter",
                "libelle" => "Ajouter des codes de promotions d'étudiants dans les référentiels",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "promo_modifier",
                "libelle" => "Modifier les codes de promotions d'étudiants dans les référentiels",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "referentiel",
                "code" => "promo_supprimer",
                "libelle" => "Supprimer des codes de promotions d'étudiants dans les référentiels",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_afficher",
                "libelle" => "Afficher les paramètres de l'application",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_ajouter",
                "libelle" => "Ajouter un paramètre de l'application",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_modifier",
                "libelle" => "Modifier un paramètre de l'application",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_supprimer",
                "libelle" => "Supprimer un paramètre de l'application",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_contrainte_cursus_afficher",
                "libelle" => "Afficher les contraintes de cursus des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_contrainte_cursus_ajouter",
                "libelle" => "Ajouter une contrainte sur le cursus des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_contrainte_cursus_modifier",
                "libelle" => "Modifier une contrainte de cursus des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "parametre_contrainte_cursus_supprimer",
                "libelle" => "Supprimer une contrainte de cursus des étudiants",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "niveau_etude_afficher",
                "libelle" => "Afficher les niveaux d'études",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "niveau_etude_ajouter",
                "libelle" => "Ajouter un niveau d'étude",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "niveau_etude_modifier",
                "libelle" => "Modifier les niveaux d'études",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "parametre",
                "code" => "niveau_etude_supprimer",
                "libelle" => "Supprimer un niveau d'étude",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementetat",
                "code" => "etat_voir",
                "libelle" => "État - Visualiser les états",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementetat",
                "code" => "etat_ajouter",
                "libelle" => "État - Ajouter un état",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementetat",
                "code" => "etat_modifier",
                "libelle" => "État - Modifier un état",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementetat",
                "code" => "etat_supprimer",
                "libelle" => "État - Supprimer un état",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementtype",
                "code" => "type_consultation",
                "libelle" => "Type - Visualiser les types",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementtype",
                "code" => "type_ajout",
                "libelle" => "Type - Ajouter un type",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementtype",
                "code" => "type_edition",
                "libelle" => "Type - Modifier un type",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementtype",
                "code" => "type_suppression",
                "libelle" => "Type - Supprimer un type",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementinstance",
                "code" => "instance_consultation",
                "libelle" => "Instance - Visualiser les instances",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementinstance",
                "code" => "instance_ajout",
                "libelle" => "Instance - Ajouter une instance",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementinstance",
                "code" => "instance_edition",
                "libelle" => "Instance - Modifier une instance",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementinstance",
                "code" => "instance_suppression",
                "libelle" => "Instance - Supprimer une instance",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "evenementinstance",
                "code" => "instance_traitement",
                "libelle" => "Instance - Traiter les instances en attente",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentmacro",
                "code" => "documentmacro_index",
                "libelle" => "Afficher l'index des macros",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentmacro",
                "code" => "documentmacro_ajouter",
                "libelle" => "Ajouter une macro",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentmacro",
                "code" => "documentmacro_modifier",
                "libelle" => "Modifier une macro",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentmacro",
                "code" => "documentmacro_supprimer",
                "libelle" => "Supprimer une macro",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documenttemplate",
                "code" => "documenttemplate_index",
                "libelle" => "Afficher l'index des templates",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documenttemplate",
                "code" => "documenttemplate_afficher",
                "libelle" => "Afficher un template",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documenttemplate",
                "code" => "documenttemplate_ajouter",
                "libelle" => "Ajouter un template",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documenttemplate",
                "code" => "documenttemplate_modifier",
                "libelle" => "Modifier un template",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documenttemplate",
                "code" => "documenttemplate_supprimer",
                "libelle" => "Supprimer un template",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentcontenu",
                "code" => "documentcontenu_index",
                "libelle" => "Accès à l'index des rendus",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentcontenu",
                "code" => "documentcontenu_afficher",
                "libelle" => "Afficher un rendus",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "documentcontenu",
                "code" => "documentcontenu_supprimer",
                "libelle" => "Supprimer un rendus ",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "mail",
                "code" => "mail_index",
                "libelle" => "Afficher les mails envoyés",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "mail",
                "code" => "mail_afficher",
                "libelle" => "Afficher un mail spécifique",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "mail",
                "code" => "mail_reenvoi",
                "libelle" => "Ré-envoi de mail",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "mail",
                "code" => "mail_supprimer",
                "libelle" => "Suppression d'un mail",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "mail",
                "code" => "mail_test",
                "libelle" => "Envoi d'un mail de test",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "mail",
                "code" => "mail_afficher_config",
                "libelle" => "Afficher la configuration des mails",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etat",
                "code" => "etat_index",
                "libelle" => "Afficher l'index des états",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etat",
                "code" => "etat_ajouter",
                "libelle" => "Ajouter un état",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etat",
                "code" => "etat_modifier",
                "libelle" => "Modifier un état",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etat",
                "code" => "etat_detruire",
                "libelle" => "Supprimer un état",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "etat",
                "code" => "etat_historiser",
                "libelle" => "Historiser/Restaurer un etat",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "storage",
                "code" => "storage_index",
                "libelle" => "Afficher l'index des storages",
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => "fichier",
                "code" => "fichier_index",
                "libelle" => "Afficher l'index des fichiers",
                "ordre" => $ordre++,
            ],
            /** Db-import */
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "import-consulter",
                "libelle" => "Détail des imports",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "import-lister",
                "libelle" => "Lister les imports",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "import-lancer",
                "libelle" => "Exectuer un script d'import",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "log-consulter",
                "libelle" => "Détail d'un log",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "log-lister",
                "libelle" => "Lister les  logs des import",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "observation-consulter",
                "libelle" => "Détail d'une obeservation d'import",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "observation-lister",
                "libelle" => "Lister les  observation des import",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "synchro-consulter",
                "libelle" => "Détail des syncrhonisation",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "synchro-lister",
                "libelle" => "Lister les syncrhonisation",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "unicaen-db-import",
                "code" => "synchro-lancer",
                "libelle" => "Exectuer un script de synchronisation",
                "ordre" => ++$ordre
            ],
        //UnicaenTag
            [
                "categorie_id" => "tag",
                "code" => "tag_index",
                "libelle" => "Afficher l'index des tags",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "tag_ajouter",
                "libelle" => "Ajouter un tag",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "tag_modifier",
                "libelle" => "Modifier un tag",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "tag_supprimer",
                "libelle" => "Supprimer un tag",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "categorie_tag_index",
                "libelle" => "Afficher l'index des catégories de tags",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "categorie_tag_ajouter",
                "libelle" => "Afficher l'index des catégories de tags",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "categorie_tag_modifier",
                "libelle" => "Modifier une categorie de tag",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tag",
                "code" => "categorie_tag_supprimer",
                "libelle" => "Supprimer une categorie de tag",
                "ordre" => ++$ordre
            ],
//            Indicateur
            [
                "categorie_id" => "indicateur",
                "code" => "afficher_indicateur",
                "libelle" => "Afficher un indicateur",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "indicateur",
                "code" => "afficher_indicateur_tous",
                "libelle" => "Afficher tous les indicateurs",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "indicateur",
                "code" => "editer_indicateur",
                "libelle" => "Éditer un indicateur",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "indicateur",
                "code" => "detruire_indicateur",
                "libelle" => "Effacer un indicateur",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "indicateur",
                "code" => "indicateur_index",
                "libelle" => "Accéder à l''index",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "indicateur",
                "code" => "indicateur_mes_indicateurs",
                "libelle" => "Affichage du menu - Mes Indicateurs -",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "abonnement",
                "code" => "afficher_abonnement",
                "libelle" => "Afficher un abonnement",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "abonnement",
                "code" => "editer_abonnement",
                "libelle" => "Éditer un abonnement",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "abonnement",
                "code" => "detruire_abonnement",
                "libelle" => "Effacer un abonnement",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tableaudebord",
                "code" => "afficher_tableaudebord",
                "libelle" => "Afficher un tableau de bord",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tableaudebord",
                "code" => "editer_tableaudebord",
                "libelle" => "Éditer un tableau de bord",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "tableaudebord",
                "code" => "detruire_tableaudebord",
                "libelle" => "Effacer un tableau de bord",
                "ordre" => ++$ordre
            ],

            /** UnicaenCalendrier */
            [
                "categorie_id" => "unicaencalendrier_index",
                "code" => "index",
                "libelle" => "Accès à l'administration du module",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendriertype",
                "code" => "calendriertype_index",
                "libelle" => "Accéder à l'index",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendriertype",
                "code" => "calendriertype_afficher",
                "libelle" => "Afficher",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendriertype",
                "code" => "calendriertype_ajouter",
                "libelle" => "Ajouter",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendriertype",
                "code" => "calendriertype_modifier",
                "libelle" => "Modifier",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendriertype",
                "code" => "calendriertype_supprimer",
                "libelle" => "Supprimer",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendrier",
                "code" => "calendrier_index",
                "libelle" => "Accéder à l'index",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendrier",
                "code" => "calendrier_afficher",
                "libelle" => "Afficher",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendrier",
                "code" => "calendrier_ajouter",
                "libelle" => "Ajouter",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendrier",
                "code" => "calendrier_modifier",
                "libelle" => "Modifier",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendrier",
                "code" => "calendrier_historiser",
                "libelle" => "Historiser/Restaurer",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "calendrier",
                "code" => "calendrier_supprimer",
                "libelle" => "Supprimer",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "datetype",
                "code" => "datetype_index",
                "libelle" => "Accéder à l'index",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "datetype",
                "code" => "datetype_afficher",
                "libelle" => "Afficher",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "datetype",
                "code" => "datetype_ajouter",
                "libelle" => "Ajouter",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "datetype",
                "code" => "datetype_modifier",
                "libelle" => "Modifier",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "datetype",
                "code" => "datetype_supprimer",
                "libelle" => "Supprimer",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "date",
                "code" => "date_index",
                "libelle" => "Accéder à l'index",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "date",
                "code" => "date_afficher",
                "libelle" => "Afficher",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "date",
                "code" => "date_ajouter",
                "libelle" => "Ajouter",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "date",
                "code" => "date_modifier",
                "libelle" => "Modifier",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "date",
                "code" => "date_historiser",
                "libelle" => "Historiser/Restaurer",
                "ordre" => ++$ordre
            ],
            [
                "categorie_id" => "date",
                "code" => "date_supprimer",
                "libelle" => "Supprimer",
                "ordre" => ++$ordre
            ],
        ];
        return $res;
    }

    public function unicaen_privilege_privilege_role_linker(): array
    {
        $data = [
            /** Gestions des étudiants */
            "etudiant" => [
                "etudiant_afficher"                     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "etudiant_ajouter"                      => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_modifier"                     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_supprimer"                    => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "etudiant_own_profil_afficher"          => [RolesProvider::ETUDIANT],
                "groupe_afficher"                       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "groupe_ajouter"                        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "groupe_modifier"                       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "groupe_supprimer"                      => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "groupe_administrer_etudiants"          => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "preference_afficher"                   => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "preference_ajouter"                    => [RolesProvider::ADMIN_TECH],
                "preference_modifier"                   => [RolesProvider::ADMIN_TECH],
                "preference_supprimer"                  => [RolesProvider::ADMIN_TECH],
                "etudiant_own_preferences_afficher"     => [RolesProvider::ETUDIANT],
                "etudiant_own_preferences_ajouter"      => [RolesProvider::ETUDIANT],
                "etudiant_own_preferences_modifier"     => [RolesProvider::ETUDIANT],
                "etudiant_own_preferences_supprimer"    => [RolesProvider::ETUDIANT],
                "disponibilite_afficher"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "disponibilite_ajouter"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "disponibilite_modifier"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "disponibilite_supprimer"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_contraintes_afficher"         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_contrainte_modifier"          => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_contrainte_valider"           => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_contrainte_invalider"         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_contrainte_activer"           => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "etudiant_contrainte_desactiver"        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            /** Terrains de stages */
            "terrain" => [
                "categorie_stage_afficher"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "categorie_stage_ajouter"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "categorie_stage_modifier"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "categorie_stage_supprimer"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "terrain_stage_afficher"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "terrain_stage_ajouter"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "terrain_stage_modifier"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "terrain_stage_supprimer"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "terrains_importer"                     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "terrains_exporter"                     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            /** Années universitaires */
            "annee" => [
                "annee_universitaire_afficher"          => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "annee_universitaire_ajouter"           => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "annee_universitaire_modifier"          => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "annee_universitaire_supprimer"         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "annee_universitaire_valider"           => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "annee_universitaire_deverrouiller"     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            /** Sessions des stages */
            "session" => [
                "session_stage_afficher"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "session_stage_ajouter"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "session_stage_modifier"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "session_stage_supprimer"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
            ],
            /** Stages */
            "stage" => [
                "stage_afficher"                        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "stage_ajouter"                         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "stage_modifier"                        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "stage_supprimer"                       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "etudiant_own_stages_afficher"          => [RolesProvider::ETUDIANT],
                "affectation_afficher"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "affectation_ajouter"                   => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "affectation_modifier"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "affectation_supprimer"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "affectation_run_procedure"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "affectation_pre_valider"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "commission_valider_affectations"       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE],
                "validation_stage_afficher"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::GARDE, RolesProvider::SCOLARITE],
                "validation_stage_modifier"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "procedure_afficher"                    => [RolesProvider::ADMIN_TECH, RolesProvider::GARDE],
                "procedure_modifier"                    => [RolesProvider::ADMIN_TECH, RolesProvider::GARDE],
            ],
            /** Contacts */
            "contact" => [
                "contact_afficher"                      => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_ajouter"                       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_modifier"                      => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_supprimer"                     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_importer"                      => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_exporter"                      => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_stage_afficher"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_stage_ajouter"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_stage_modifier"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_stage_supprimer"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_terrain_afficher"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_terrain_ajouter"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_terrain_modifier"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_terrain_supprimer"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_terrain_importer"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "contact_terrain_exporter"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "send_mail_validation"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            /** Convention de stage */
            "convention" => [
                "convention_afficher"                   => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "convention_televerser"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "convention_generer"                    => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "convention_modifier"                   => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "convention_supprimer"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "convention_telecharger"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "modele_convention_afficher"            => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "modele_convention_ajouter"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "modele_convention_modifier"            => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "modele_convention_supprimer"           => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            /** Lib Unicaen-Utilisateur */
            "utilisateur" => [
                "utilisateur_afficher"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "utilisateur_ajouter"                   => [RolesProvider::ADMIN_TECH],
                "utilisateur_changerstatus"             => [RolesProvider::ADMIN_TECH],
                "utilisateur_modifierrole"              => [RolesProvider::ADMIN_TECH],
                "utilisateur_rechercher"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
            ],
            /** Lib Unicaen-Roles */
            "role" => [
                "role_afficher"                         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "role_modifier"                         => [RolesProvider::ADMIN_TECH],
                "role_effacer"                          => [RolesProvider::ADMIN_TECH],
            ],
            /** Lib Unicaen-Privileges */
            "privilege" => [
                "privilege_voir"                        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "privilege_ajouter"                     => [RolesProvider::ADMIN_TECH],
                "privilege_modifier"                    => [RolesProvider::ADMIN_TECH],
                "privilege_supprimer"                   => [RolesProvider::ADMIN_TECH],
                "privilege_affecter"                    => [RolesProvider::ADMIN_TECH],
            ],
            /** Messages */
            "message" => [
                "message_info_afficher"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "message_info_ajouter"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "message_info_modifier"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "message_info_supprimer"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            "faq" => [
                "faq_question_afficher"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_question_ajouter"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_question_modifier"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_question_supprimer"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_categorie_afficher"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_categorie_ajouter"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_categorie_modifier"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "faq_categorie_supprimer"               => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
           /** Référentiel de données */
            "referentiel" => [
                "source_afficher"                       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "source_ajouter"                        => [RolesProvider::ADMIN_TECH],
                "source_modifier"                       => [RolesProvider::ADMIN_TECH],
                "source_supprimer"                      => [RolesProvider::ADMIN_TECH],
                "promo_afficher"                        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "promo_ajouter"                         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "promo_modifier"                        => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "promo_supprimer"                       => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
            ],
            /** Paramétres */
            "parametre" => [
                "parametre_afficher"                    => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "parametre_ajouter"                     => [RolesProvider::ADMIN_TECH],
                "parametre_modifier"                    => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "parametre_supprimer"                   => [RolesProvider::ADMIN_TECH],
                "parametre_contrainte_cursus_afficher"  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "parametre_contrainte_cursus_ajouter"   => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "parametre_contrainte_cursus_modifier"  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "parametre_contrainte_cursus_supprimer" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "niveau_etude_afficher"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "niveau_etude_ajouter"                  => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "niveau_etude_modifier"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "niveau_etude_supprimer"                => [RolesProvider::ADMIN_TECH],
            ],
            "evenementetat" =>  [
                "etat_voir"                             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "etat_ajouter"                            => [RolesProvider::ADMIN_TECH],
                "etat_modifier"                          => [RolesProvider::ADMIN_TECH],
                "etat_supprimer"                      => [RolesProvider::ADMIN_TECH],
            ],
            "evenementtype" => [
                "type_consultation"                     => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "type_ajout"                            => [RolesProvider::ADMIN_TECH],
                "type_edition"                          => [RolesProvider::ADMIN_TECH],
                "type_suppression"                      => [RolesProvider::ADMIN_TECH],
            ],
            "evenementinstance" => [
                "instance_consultation"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "instance_ajout"                        => [RolesProvider::ADMIN_TECH],
                "instance_edition"                      => [RolesProvider::ADMIN_TECH],
                "instance_suppression"                  => [RolesProvider::ADMIN_TECH],
                "instance_traitement"                   => [RolesProvider::ADMIN_TECH],
            ],
            /** UnicaenRenderer */
            "documentmacro" => [
                "documentmacro_index"                   => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "documentmacro_ajouter"                 => [RolesProvider::ADMIN_TECH],
                "documentmacro_modifier"                => [RolesProvider::ADMIN_TECH],
                "documentmacro_supprimer"               => [RolesProvider::ADMIN_TECH],
            ],
            "documenttemplate" => [
                "documenttemplate_index"                => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "documenttemplate_afficher"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "documenttemplate_ajouter"              => [RolesProvider::ADMIN_TECH],
                "documenttemplate_modifier"             => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "documenttemplate_supprimer"            => [RolesProvider::ADMIN_TECH],
            ],
            "documentcontenu" => [
                "documentcontenu_index"                 => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "documentcontenu_afficher"              => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "documentcontenu_supprimer"             => [RolesProvider::ADMIN_TECH],
            ],
            /** UnicaenMail */
            "mail" => [
                "mail_index"                            => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "mail_afficher"                         => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC, RolesProvider::SCOLARITE],
                "mail_reenvoi"                          => [RolesProvider::ADMIN_TECH],
                "mail_supprimer"                        => [RolesProvider::ADMIN_TECH],
                "mail_test"                             => [RolesProvider::ADMIN_TECH],
                "mail_afficher_config"                  => [RolesProvider::ADMIN_TECH],
            ],
                //!!! ce privilége donne accés a des parmétres de conf. Seul l'admin Tech doit y avoir accés
            /** UnicaenEtat */
            "etat" => [
                "etat_index"                            => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "etat_ajouter"                          => [RolesProvider::ADMIN_TECH],
                "etat_detruire"                         => [RolesProvider::ADMIN_TECH],
                "etat_modifier"                         => [RolesProvider::ADMIN_TECH],
                "etat_historiser"                       => [RolesProvider::ADMIN_TECH],
            ],
            /** UnicaenStorage/Fichier */
            "storage" => [
                "storage_index"                         => [RolesProvider::ADMIN_TECH],
            ],
            "fichier" => [
                "fichier_index"                         => [RolesProvider::ADMIN_TECH],
            ],
            /** DbImport */
            "unicaen-db-import" => [
                "import-consulter" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "import-lister" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "import-lancer" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "log-consulter" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "log-lister" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "observation-consulter" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "observation-lister" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "synchro-consulter" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "synchro-lister" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "synchro-lancer" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
            ],
            /** UnicaenTag */
            "tag" => [
                "tag_index" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "tag_ajouter" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "tag_modifier" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "tag_supprimer" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "categorie_tag_index" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "categorie_tag_ajouter" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "categorie_tag_modifier" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "categorie_tag_supprimer" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
            ],
            /** UnicaenIndicateur */
            "indicateur" => [
                "afficher_indicateur" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "afficher_indicateur_tous" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "editer_indicateur" => [RolesProvider::ADMIN_TECH],
                "detruire_indicateur" => [RolesProvider::ADMIN_TECH],
                "indicateur_index" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "indicateur_mes_indicateurs" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
            ],
            "abonnement" => [
                "afficher_abonnement" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "editer_abonnement" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "detruire_abonnement" => [RolesProvider::ADMIN_TECH],
            ],
            "tableaudebord" => [
                "afficher_tableaudebord" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "editer_tableaudebord" => [RolesProvider::ADMIN_TECH, RolesProvider::ADMIN_FONC],
                "detruire_tableaudebord" => [RolesProvider::ADMIN_TECH],
            ],

            /** UnicaenCalendrier */
            "calendrier" => [ //Seul l'administrateur Technique peux acceder directement aux calendrier
                //La modifications des instances de dates passe par l'entité associé (ie : les dates d'une session de stage)
                "index" => [RolesProvider::ADMIN_TECH],
                "calendriertype_afficher" => [RolesProvider::ADMIN_TECH],
                "calendriertype_index" => [RolesProvider::ADMIN_TECH],
                "calendriertype_ajouter" => [RolesProvider::ADMIN_TECH],
                "calendriertype_modifier" => [RolesProvider::ADMIN_TECH],
                "calendriertype_supprimer" => [RolesProvider::ADMIN_TECH],
                "calendrier_afficher" => [RolesProvider::ADMIN_TECH],
                "calendrier_index" => [RolesProvider::ADMIN_TECH],
                "calendrier_ajouter" => [RolesProvider::ADMIN_TECH],
                "calendrier_modifier" => [RolesProvider::ADMIN_TECH],
                "calendrier_historiser" => [RolesProvider::ADMIN_TECH],
                "calendrier_supprimer" => [RolesProvider::ADMIN_TECH],
                "datetype_afficher" => [RolesProvider::ADMIN_TECH],
                "datetype_index" => [RolesProvider::ADMIN_TECH],
                "datetype_ajouter" => [RolesProvider::ADMIN_TECH],
                "datetype_modifier" => [RolesProvider::ADMIN_TECH],
                "datetype_supprimer" => [RolesProvider::ADMIN_TECH],
                "date_afficher" => [RolesProvider::ADMIN_TECH],
                "date_index" => [RolesProvider::ADMIN_TECH],
                "date_ajouter" => [RolesProvider::ADMIN_TECH],
                "date_modifier" => [RolesProvider::ADMIN_TECH],
                "date_historiser" => [RolesProvider::ADMIN_TECH],
                "date_supprimer" => [RolesProvider::ADMIN_TECH],
            ],
        ];
        $res = [];
        foreach ($data as $categorie  => $privileges) {
            foreach ($privileges as $privilege => $roles) {
                foreach ($roles as $role) {
                    $res[] = ['role_id' => $role, 'privilege_id' => $privilege];
                }
            }
        }
        return $res;
    }
}