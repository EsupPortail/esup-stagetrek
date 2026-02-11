<?php

namespace BddAdmin;

use Application\Provider\Roles\UserProvider;
use BddAdmin\Data\AdresseDataProvider;
use BddAdmin\Data\AlgoAffectationDataProvider;
use BddAdmin\Data\ContactDataProvider;
use BddAdmin\Data\Factory\UnicaenUtilisateurDataProviderFactory;
use BddAdmin\Data\FAQDataProvider;
use BddAdmin\Data\NiveauEtudeDataProvider;
use BddAdmin\Data\SourceDataProvider;
use BddAdmin\Data\TerrainDataProvider;
use BddAdmin\Data\UnicaenCalendrierDataProvider;
use BddAdmin\Data\UnicaenEtatDataProvider;
use BddAdmin\Data\UnicaenEvenementDataProvider;
use BddAdmin\Data\UnicaenFichierDataProvider;
use BddAdmin\Data\UnicaenParametreDataProvider;
use BddAdmin\Data\UnicaenPrivilegeDataProvider;
use BddAdmin\Data\UnicaenRendererDataProvider;
use BddAdmin\Data\UnicaenRoleDataProvider;
use BddAdmin\Data\UnicaenTagDataProvider;
use BddAdmin\Data\UnicaenUtilisateurDataProvider;

return [
    'unicaen-bddadmin' => [
        'data' => [
            'config'  => [
                /** Table Diverses **/
                'source' => SourceDataProvider::getConfig('source'),
                'adresse_type' => AdresseDataProvider::getConfig('adresse_type'),
                'faq_categorie_question' => FAQDataProvider::getConfig('faq_categorie_question'),
                'contact' => ContactDataProvider::getConfig('contact'),
                'terrain_stage_niveau_demande' => TerrainDataProvider::getConfig('terrain_stage_niveau_demande'),
                'niveau_etude' => NiveauEtudeDataProvider::getConfig('niveau_etude'),

                /** Paramétrage */
                'parametre_categorie' => UnicaenParametreDataProvider::getConfig('parametre_categorie'),
                'parametre_type' => UnicaenParametreDataProvider::getConfig('parametre_type'),
                'parametre' => UnicaenParametreDataProvider::getConfig('parametre'),
                'parametre_cout_affectation' => UnicaenParametreDataProvider::getConfig('parametre_cout_affectation'),

                /** Algorithmes */
                'procedure_affectation' => AlgoAffectationDataProvider::getConfig('procedure_affectation'),

                /** Utilisateur / Priviléges / Roles **/
                'unicaen_utilisateur_role' => UnicaenRoleDataProvider::getConfig('unicaen_utilisateur_role'),
                'unicaen_utilisateur_user' => UnicaenUtilisateurDataProvider::getConfig('unicaen_utilisateur_user'),
                'unicaen_utilisateur_role_linker' => UnicaenUtilisateurDataProvider::getConfig('unicaen_utilisateur_role_linker'),
                'unicaen_privilege_categorie' => UnicaenPrivilegeDataProvider::getConfig('unicaen_privilege_categorie'),
                'unicaen_privilege_privilege' => UnicaenPrivilegeDataProvider::getConfig('unicaen_privilege_privilege'),
                'unicaen_privilege_privilege_role_linker' => UnicaenPrivilegeDataProvider::getConfig('unicaen_privilege_privilege_role_linker'),

                /** UnicaenEtat */
                'unicaen_etat_categorie' => UnicaenEtatDataProvider::getConfig('unicaen_etat_categorie'),
                'unicaen_etat_type' => UnicaenEtatDataProvider::getConfig('unicaen_etat_type'),
                'unicaen_tag_categorie' => UnicaenTagDataProvider::getConfig('unicaen_tag_categorie'),
                'unicaen_tag' => UnicaenTagDataProvider::getConfig('unicaen_tag'),


                /** UnicaenEtat */
                'unicaen_evenement_etat' => UnicaenEvenementDataProvider::getConfig('unicaen_evenement_etat'),
                'unicaen_evenement_type' => UnicaenEvenementDataProvider::getConfig('unicaen_evenement_type'),

                /** UnicaenFichier */
                'unicaen_fichier_nature' => UnicaenFichierDataProvider::getConfig('unicaen_evenement_type'),

                /** UnicaenRenderer */
                'unicaen_renderer_macro' => UnicaenRendererDataProvider::getConfig('unicaen_renderer_macro'),
                'unicaen_renderer_template' => UnicaenRendererDataProvider::getConfig('unicaen_renderer_template'),

                /**
                 * UnicaenCalendrier *
                 */
                'unicaen_calendrier_calendrier_type' => UnicaenCalendrierDataProvider::getConfig('unicaen_calendrier_calendrier_type'),
                'unicaen_calendrier_date_type' => UnicaenCalendrierDataProvider::getConfig('unicaen_calendrier_date_type'),
                'unicaen_calendrier_calendriertype_datetype' => UnicaenCalendrierDataProvider::getConfig('unicaen_calendrier_calendriertype_datetype'),


            ],

            'sources' => [
                /** Tables diverses **/
                'source' => SourceDataProvider::class,
                'contact' => ContactDataProvider::class,
                'adresse_type' => AdresseDataProvider::class,
                'faq_categorie_question' => FAQDataProvider::class,
                'terrain_stage_niveau_demande' =>  TerrainDataProvider::class,
                'niveau_etude' =>  NiveauEtudeDataProvider::class,

                /** Paramétrages */
                'parametre_categorie' => UnicaenParametreDataProvider::class,
                'parametre_type' => UnicaenParametreDataProvider::class,
                'parametre' =>  UnicaenParametreDataProvider::class,
                'parametre_cout_affectation' =>  UnicaenParametreDataProvider::class,

                /** Algorithmes */
                'procedure_affectation' =>  AlgoAffectationDataProvider::class,

                /** Utilisateur / Priviléges / Roles **/
                'unicaen_utilisateur_role' => UnicaenRoleDataProvider::class,
                'unicaen_utilisateur_role_linker' => UnicaenUtilisateurDataProvider::class,
                'unicaen_privilege_categorie' => UnicaenPrivilegeDataProvider::class,
                'unicaen_privilege_privilege' =>  UnicaenPrivilegeDataProvider::class,
                'unicaen_privilege_privilege_role_linker' => UnicaenPrivilegeDataProvider::class,
                'unicaen_utilisateur_user' => UnicaenUtilisateurDataProvider::class,

                /** UnicaenEtat */
                'unicaen_etat_categorie' => UnicaenEtatDataProvider::class,
                'unicaen_etat_type' => UnicaenEtatDataProvider::class,

                /** UnicaenEtat */
                'unicaen_evenement_etat' => UnicaenEvenementDataProvider::class,
                'unicaen_evenement_type' => UnicaenEvenementDataProvider::class,
                'unicaen_tag_categorie' => UnicaenTagDataProvider::class,
                'unicaen_tag' => UnicaenTagDataProvider::class,
                /** UnicaenFichier */
                'unicaen_fichier_nature' => UnicaenFichierDataProvider::class,
                /** UnicaenFichier */
                'unicaen_renderer_macro' => UnicaenRendererDataProvider::class,
                'unicaen_renderer_template' => UnicaenRendererDataProvider::class,
                /**
                 * UnicaenCalendrier
                 */
                'unicaen_calendrier_calendrier_type' => UnicaenCalendrierDataProvider::class,
                'unicaen_calendrier_date_type' => UnicaenCalendrierDataProvider::class,
                'unicaen_calendrier_calendriertype_datetype' => UnicaenCalendrierDataProvider::class,
            ],
        ],

        'histo' => [
            'user_id' => UserProvider::APP_USER_ID,
        ],
    ],
    'service_manager' => [
        'factories' => [
            UnicaenUtilisateurDataProvider::class => UnicaenUtilisateurDataProviderFactory::class,
        ],
    ],
];