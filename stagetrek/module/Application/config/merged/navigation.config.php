<?php

namespace Application;

use Application\Controller\Affectation\ProcedureAffectationController;
use Application\Controller\AnneeUniversitaire\AnneeUniversitaireController;
use Application\Controller\Contact\ContactController;
use Application\Controller\Contrainte\ContrainteCursusController;
use Application\Controller\Convention\ModeleConventionController;
use Application\Controller\Etudiant\EtudiantController;
use Application\Controller\Groupe\GroupeController;
use Application\Controller\Notification\FaqCategorieController;
use Application\Controller\Notification\FaqQuestionController;
use Application\Controller\Notification\MessageInfoController;
use Application\Controller\Parametre\NiveauEtudeController;
use Application\Controller\Parametre\ParametreController;
use Application\Controller\Parametre\ParametreCoutAffectationController;
use Application\Controller\Parametre\ParametreCoutTerrainController;
use Application\Controller\Referentiel\ReferentielPromoController;
use Application\Controller\Referentiel\SourceController;
use Application\Controller\Stage\SessionStageController;
use Application\Controller\Stage\StageController;
use Application\Controller\Terrain\CategorieStageController;
use Application\Controller\Terrain\TerrainStageController;
use Laminas\Router\Http\Literal;
use UnicaenDbImport\Privilege\ImportPrivilege;
use UnicaenDbImport\Privilege\SynchroPrivilege;
use UnicaenEtat\Controller\EtatCategorieController;
use UnicaenEtat\Controller\EtatTypeController;
use UnicaenIndicateur\Controller\IndexController as IndicateurIndexController;
use \UnicaenRenderer\Controller\IndexController as RendererIndexController;
use UnicaenCalendrier\Controller\IndexController as CalendrierIndexController;
use UnicaenEvenement\Provider\Privilege\EvenementinstancePrivileges;
use UnicaenIndicateur\Controller\IndicateurController;
use UnicaenMail\Provider\Privilege\MailPrivileges;
use UnicaenPrivilege\Controller\PrivilegeCategorieController;
use UnicaenPrivilege\Guard\PrivilegeController;
use UnicaenRenderer\Controller\MacroController;
use UnicaenRenderer\Controller\RenduController;
use UnicaenRenderer\Controller\TemplateController;
use UnicaenTag\Controller\TagCategorieController;
use UnicaenTag\Controller\TagController;
use UnicaenUtilisateur\Controller\RoleController;
use UnicaenUtilisateur\Controller\UtilisateurController;

//TODO : revoir les roles et les priviléges permettant d'accéder à certains "sous-menue"
//Reprendre la corrections des fautes de francais
//Maj de la pré-productions
//intégration en local des différences sur les modéles ...


$order = 1;
return [
    //Définition de "fausses routes" administration et config même si elle pointe vers autres chose
    'router' => [
        'routes' => [
            'configuration' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/configuration',
                ],
                'may_terminate' => false,
            ],
            'administration' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/administration',
                ],
                'may_terminate' => false,
            ],
        ],
    ],

    "navigation" => [
        "default" => [
            "home" => [
                "pages" => [
//                    //Pages d"un étudiant
                    "mon-profil" => [
                        "label" => "Mon profil",
                        "title" => "Mon profil",
                        "route" => EtudiantController::ROUTE_MON_PROFIL,
                        "resource" => PrivilegeController::getResourceId(EtudiantController::class, EtudiantController::ACTION_MON_PROFIL),
                        "order" => $order++,
                    ],
                    "mes-stages" => [
                        "label" => "Mes stages",
                        "title" => "Mes stages",
                        "route" => StageController::ROUTE_MES_STAGES,
                        "resource" => PrivilegeController::getResourceId(StageController::class, StageController::ACTION_MES_STAGES),
                        "order" => $order++,
                    ],
                    //Gestion des étudiants
                    "etudiant" => [
                        "label" => "Étudiants",
                        "title" => "Gestion des étudiants",
                        "route" => EtudiantController::ROUTE_INDEX,
                        "icon" => "fas fa-users",
                        "order" => $order++,
                        "pages" => [
                            [
                                "label" => 'Étudiants',
                                "title" => "Gestion des étudiants",
                                "route" => EtudiantController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(EtudiantController::class, EtudiantController::ACTION_INDEX),
                                "icon" => "fas fa-user",
                                "order" => $order++,
                            ],
                            [
                            "label" => "Groupe d’étudiants",
                            "title" => "Gestion des groupes d'étudiants",
                            "route" => GroupeController::ROUTE_INDEX,
                            "resource" => PrivilegeController::getResourceId(GroupeController::class, GroupeController::ACTION_INDEX),
                            "icon" => "fas fa-users",
                            "order" => $order++,
                            ],
                        ],
                    ],
                    "stages" => [
                        "label" => "Stages",
                        "title" => "Gestion des stages",
                        "route" => SessionStageController::ROUTE_INDEX,
                        "icon" => "fas fa-notes-medical",
                        "order" => $order++,
                        "pages" => [
                            [
                                "label" => "Année universitaire",
                                "title" => "Calendrier de l'année en cours",
                                "route" => AnneeUniversitaireController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(AnneeUniversitaireController::class, AnneeUniversitaireController::ACTION_INDEX),
                                "icon" => "fas fa-calendar",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Sessions de stages",
                                "title" => "Gestion des sessions de stage",
                                "route" => SessionStageController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(SessionStageController::class, SessionStageController::ACTION_INDEX),
                                "icon" => "fas fa-briefcase-medical",
                                "order" => $order++,
                            ],
                        ],
                    ],
                    "terrains" => [
                        "label" => "Terrains",
                        "title" => "Gestion des terrains de stages",
                        "route" => TerrainStageController::ROUTE_INDEX,
                        "order" => $order++,
                        "icon" => "fas fa-house-medical",
                        "pages" => [
                            [
                                "label" => "Catégories de stages",
                                "title" => "Gestion des catégories de terrains de stages",
                                "route" => CategorieStageController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(CategorieStageController::class, CategorieStageController::ACTION_INDEX),
                                "icon" => "fas fa-hospital",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Terrains de stages",
                                "title" => "Gestion des terrains de stages",
                                "route" => TerrainStageController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(TerrainStageController::class, TerrainStageController::ACTION_INDEX),
                                "icon" => "fas fa-house-medical",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Contraintes de cursus",
                                "title" => "Gestion des contraintes de cursus des étudiants",
                                "route" => ContrainteCursusController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ContrainteCursusController::class, ContrainteCursusController::ACTION_INDEX),
                                "icon" => "fas fa-check-square",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Encadrants pédagogiques",
                                "title" => "Encadrants des pédagogiques des stages",
                                "route" => ContactController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ContactController::class, ContactController::ACTION_INDEX),
                                "icon" => "fas fa-user-doctor",
                                "order" => $order++,
                            ],
                        ]
                    ],
                    //Pages d"administrations
                    "administration" => [
                        "label" => "Administration",
                        "title" => "Administration de l\'application",
                        "route" => "administration",
                        "icon" => "fas fa-gears",
                        "order" => $order++,
                        "pages" => [
                            /////////////////////
                            // Utilisateurs
                            /////////////////////
                            [
                                "label" => "Utilisateurs et privilèges",
                                "title" => "Administation des droits d'accés",
                                "route" => "unicaen-utilisateur",
                                "dropdown-header" => true,
                                "icon" => "fas fa-users-gear",
                                "resource" => PrivilegeController::getResourceId(UtilisateurController::class, 'index'),
                                "order" => $order++,
                            ],
                            [
                                "label" => "Gérer les utilisateurs",
                                "title" => "Gérer les utilisateurs",
                                "route" => "unicaen-utilisateur",
//                                "resource" => UtilisateurPrivileges::getResourceId(UtilisateurPrivileges::UTILISATEUR_AFFICHER),
                                "resource" => PrivilegeController::getResourceId(UtilisateurController::class, 'index'),
                                "icon" => "fas fa-angle-right",
                                "order" =>  $order++,
                            ],
                            [
                                "label" => "Gérer les rôles",
                                "title" => "Gérer les rôles",
                                "route" => "unicaen-role",
                                "resource" => PrivilegeController::getResourceId(RoleController::class, 'index'),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Gérer les privilèges",
                                "title" => "Gérer les privilèges",
                                "route" => "unicaen-privilege",
                                "resource" => PrivilegeController::getResourceId(PrivilegeCategorieController::class, 'index'),
                                "order" => $order++,
                                "icon" => "fas fa-angle-right",
                            ],
                            /////////////////////
                            // Communication
                            /////////////////////
                            [
                                "label" => "Communication",
                                "title" => "Communication interne",
                                "route" => MessageInfoController::ROUTE_INDEX,
                                "dropdown-header" => true,
                                "icon" => "fas fa-comment",
                                "resource" => PrivilegeController::getResourceId(MessageInfoController::class, 'index'),
                                "order" => $order++,
                            ],
                            [
                                "label" => "Messages d'informations",
                                "title" => "Gestion des messages d'informations",
                                "route" =>  MessageInfoController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(MessageInfoController::class, 'index'),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "FAQ - Catégories",
                                "title" => "Gestion des catégories de questions",
                                "route" => FaqCategorieController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(FaqCategorieController::class, 'index'),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "FAQ - Questions",
                                "title" => "Gestion de la Faq",
                                "route" => FaqQuestionController::ROUTE_INDEX,
//                                Cas spécifique : l'acces a la FAQ pour la  consultation est géré en dehors du menu d'administration dans les aides
//                                La navigation ici sert pour de l'administration d'ou le fait que l'on regarde l'action modifier (même si techniquement l'accés a la page est autorisé pour tous, c'est l'affichage de l'onglet que l'on gére ici
                                "resource" => PrivilegeController::getResourceId(FaqQuestionController::class, FaqQuestionController::ACTION_AJOUTER),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            'indicateurs' => [
                                'label' => 'Indicateurs',
                                'route' => 'indicateurs',
                                'resource' => PrivilegeController::getResourceId(IndicateurController::class, 'index'),
                                'order' => $order++,
                                'icon' => 'fas fa-angle-right',
                            ],
                            'mes-indicateurs' => [
                                'label' => 'Mes indicateurs',
                                'route' => 'mes-indicateurs',
                                'resource' => PrivilegeController::getResourceId(IndicateurIndexController::class, 'index'),
                                'order' => $order++,
                                'icon' => 'fas fa-angle-right',
                            ],
                            [
                                "label" => "Mails",
                                "title" => "Gestion des mails",
                                "route" => "mail",
                                "resource" => MailPrivileges::getResourceId(MailPrivileges::MAIL_AFFICHER),
                                "order" => $order++,
                                "icon" => "fas fa-angle-right",
                            ],
                            /////////////////////
                            // Documents
                            /////////////////////
                            [
                                "label" => "Documents",
                                "title" => "Gestion des documents et de la communication interne",
                                "route" => "contenu/rendu",
                                "dropdown-header" => true,
                                "icon" => "fas fa-file",
                                "resource" => PrivilegeController::getResourceId(RendererIndexController::class, 'index'),
                                "order" => $order++,
                            ],
                            [
                                "label" => "Gestion des rendus",
                                "title" => "Gestion des rendus",
                                "route" => "contenu/rendu",
                                "icon" => "fas fa-angle-right",
                                "resource" => PrivilegeController::getResourceId(RenduController::class, 'index'),
                                "order" => $order++,
                            ],
                            [
                                "label" => "Gestion des templates",
                                "title" => "Gestion des templates",
                                "route" => "contenu/template",
                                "icon" => "fas fa-angle-right",
                                "resource" => PrivilegeController::getResourceId(TemplateController::class, 'index'),
                                "order" => $order++,
                            ],
                            [
                                "label" => "Gestion des macros",
                                "title" => "Gestion des macros",
                                "route" => "contenu/macro",
                                "icon" => "fas fa-angle-right",
                                "resource" => PrivilegeController::getResourceId(MacroController::class, 'index'),
                                "order" => $order++,
                            ],
                            [
                                "label" => "Conventions de stages",
                                "title" => "Gestion des modéles de convention",
                                "route" => ModeleConventionController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ModeleConventionController::class, ModeleConventionController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            /////////////////////
                            // Paramètres
                            /////////////////////
                            [
                                "label" => "Paramètres",
                                "title" => "Paramètres",
                                "route" => ParametreController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ParametreController::class, ParametreController::ACTION_INDEX),
                                "dropdown-header" => true,
                                "icon" => "fas fa-cog",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Paramètres applicatifs",
                                "title" => "Gestion des paramètres applicatifs",
                                "route" => ParametreController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ParametreController::class, ParametreController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Coûts des affectations",
                                "title" => "Gestion des paramètres de coûts des affectations",
                                "route" => ParametreCoutAffectationController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ParametreCoutAffectationController::class, ParametreCoutAffectationController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Coûts des terrains spécifiques",
                                "title" => "Gestion des paramètres de coûts de terrains",
                                "route" => ParametreCoutTerrainController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ParametreCoutTerrainController::class, ParametreCoutTerrainController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Procédures d'affectations",
                                "title" => "Gestion des procédures d'affectations",
                                "route" => ProcedureAffectationController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ProcedureAffectationController::class, ProcedureAffectationController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            /////////////////////
                            // Référentiels
                            /////////////////////
                            [
                                "label" => "Référentiels",
                                "title" => "Référentiels",
                                "route" =>  ReferentielPromoController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ReferentielPromoController::class, ProcedureAffectationController::ACTION_INDEX),
                                "dropdown-header" => true,
                                "icon" => "fas fa-address-book",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Reférentiels de promos",
                                "title" => "Liste des référentiels d'étudiants",
                                "route" => ReferentielPromoController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(ReferentielPromoController::class, ReferentielPromoController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Niveau d'études",
                                "title" => "Gestion des niveaux d'années d'études",
                                "route" => NiveauEtudeController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(NiveauEtudeController::class, NiveauEtudeController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                "label" => "Sources de références",
                                "title" => "Liste des sources",
                                "route" => SourceController::ROUTE_INDEX,
                                "resource" => PrivilegeController::getResourceId(SourceController::class, SourceController::ACTION_INDEX),
                                "icon" => "fas fa-angle-right",
                                "order" => $order++,
                            ],
                            [
                                'label' => "Imports",
                                "title" => "Imports des données",
                                'route' => 'unicaen-db-import/import',
                                "icon" => "fas fa-angle-right",
                                "resource" => ImportPrivilege::getResourceId(ImportPrivilege::LISTER),
                                "order" => $order++,
                            ],
                            [
                                'label' => "Synchros",
                                "title" => "Synchronisations des données",
                                'route' => 'unicaen-db-import/synchro',
                                "icon" => "fas fa-angle-right",
                                "resource" => SynchroPrivilege::getResourceId(SynchroPrivilege::LISTER),
                                "order" => $order++,
                            ],
//                            [
//                                'label' => "Logs",
//                                "title" => "Logs des imports et synchro",
//                                'route' => 'unicaen-db-import/log',
//                                "icon" => "fas fa-angle-right",
//                                "resource" => LogPrivilege::getResourceId(LogPrivilege::LISTER),
//                                "order" => $order++,
//                            ],
//                            [
//                                'label' => "Observations",
//                                "title" => "Observations des imports et synchro",
//                                'route' => 'unicaen-db-import/observ',
//                                "icon" => "fas fa-angle-right",
//                                "resource" => ObservationPrivilege::getResourceId(ObservationPrivilege::LISTER),
//                                "order" => $order++,
//                            ],

                            /////////////////////
                            // UnicaenEtat
                            /////////////////////
                            [
                                "label" => "États des entités",
                                "title" => "Gestion des états",
                                "route" => "unicaen-etat/etat-type",
                                "resource" =>  PrivilegeController::getResourceId(EtatTypeController::class, 'index'),
                                "dropdown-header" => true,
                                "order" => $order++,
                                "icon" => "fas fa-flag",
                            ],
                            [
                                "label" => "Catégories d'états",
                                "title" => "Gestion des catégories d'états",
                                "route" => "unicaen-etat/etat-categorie",
                                "resource" =>  PrivilegeController::getResourceId(EtatCategorieController::class, 'index'),
                                "order" => $order++,
                                "icon" => "fas fa-angle-right",
                            ],
                            [
                                "label" => "Types d'états",
                                "title" => "Gestion des types d'états",
                                "route" => "unicaen-etat/etat-type",
                                "resource" =>  PrivilegeController::getResourceId(EtatTypeController::class, 'index'),
                                "order" => $order++,
                                "icon" => "fas fa-angle-right",
                            ],

                            [
                                "label" => "Catégories de tags",
                                "title" => "Gestion des catégories de tags",
                                "route" => "unicaen-tag/tag-categorie",
                                "resource" =>  PrivilegeController::getResourceId(TagCategorieController::class, 'index'),
                                "order" => $order++,
                                "icon" => "fas fa-angle-right",
                            ],
                            [
                                "label" => "Tags",
                                "title" => "Gestion des tags",
                                "route" => "unicaen-tag",
                                "resource" =>  PrivilegeController::getResourceId(TagController::class, 'index'),
                                "order" => $order++,
                                "icon" => "fas fa-angle-right",
                            ],

                            /////////////////////
                            // Unicaen-Evenements
                            /////////////////////
                            'unicaen-evenement' => [
                                'label' => 'Événements',
                                "title" => "Administrations des événements",
                                'route' => 'unicaen-evenement',
                                'resource' => EvenementinstancePrivileges::getResourceId(EvenementinstancePrivileges::INSTANCE_CONSULTATION),
                                'order'    => $order++,
                                "dropdown-header" => true,
                                'icon' => 'fas fa-calendar',
                            ],
                            [
                                'label' => 'Gestion des événements',
                                "title" => "Gestion des événements",
                                'route' => 'unicaen-evenement',
                                'resource' => EvenementinstancePrivileges::getResourceId(EvenementinstancePrivileges::INSTANCE_CONSULTATION),
                                'order'    => $order++,
                                'icon' => 'fas fa-angle-right',
                            ],

                            [
                                "label" => "Calendrier",
                                "title" => "Gestion des calendrier",
                                "route" => "unicaen-calendrier",

                                'resource' => PrivilegeController::getResourceId(CalendrierIndexController::class, 'index'),
                                "dropdown-header" => true,
                                "icon" => "fas fa-calendar",
                                "order" => $order++,
                            ],

                            'unicaen-calendrier' => [
                                'label' => 'Gestion des calendriers',
                                'route' => 'unicaen-calendrier',
                                'resource' => PrivilegeController::getResourceId(CalendrierIndexController::class, 'index'),
                                'order' => $order++,
                                'icon' => 'fas fa-angle-right',
                                'pages' => [
                                ],
                            ],
                        ],
                    ],
                    // Suppression des routes de navigations fournis par des librairies
                    'unicaen-db-import' => null,
                    //Désactivations des menu de navigatins du footer
                    'etab' => null,
                    'apropos' => null,
                    'contact' => null,
                    'plan' => null,
                    'mentions-legales'  => null,
                    'informatique-et-libertes'  => null,
                ],
            ],
        ],
    ],
];


