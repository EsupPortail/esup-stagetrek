<?php

namespace BddAdmin\Data;

use Application\Entity\Db\Parametre;
use Application\Entity\Db\ParametreCategorie;
use Application\Entity\Db\ParametreType;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenParametreDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
//            TODO : passer à unicaenParametre
//            case 'unicaen_parametre_categorie' :
//                $defaultConfig = [
//                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
//                    'key'     => 'code',
//                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
//                ];
//            break;
//            case 'unicaen_parametre_parametre' :
//                $defaultConfig = [
//                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
//                    'key'     => 'code',
//                    'options' => ['update'  => true, 'soft-delete' => false, 'delete' => false,
//                        'columns' => ['categorie_id' => ['transformer' => 'select id from unicaen_parametre_categorie where code = %s'],
//                        ]]
//                ];
//            break;
            case 'parametre_type' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update'  => true, 'soft-delete' => false, 'delete' => false,]
                ];
            break;
            case 'parametre_categorie' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update'  => false, 'soft-delete' => false, 'delete' => false,]
                ];
            break;
            case 'parametre' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update'  => false, 'soft-delete' => false, 'delete' => false,
                        'columns' => [
                            'categorie_id' => ['transformer' => 'select id from parametre_categorie where code = %s'],
                            'parametre_type_id' => ['transformer' => 'select id from parametre_type where code = %s'],
                    ]]
                ];
            break;
            case 'parametre_cout_affectation' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'rang',
                    'options' => ['update'  => false, 'soft-delete' => false, 'delete' => false,]
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }



    public function parametre_categorie() : array
    {
        $ordre = 1;
        return [
            [
                "code" => ParametreCategorie::DATE,
                "libelle" => 'Dates des stages',
                "ordre" => $ordre++,
            ],
            [
                "code" =>  ParametreCategorie::PREFERENCES,
                "libelle" => 'Préférences',
                "ordre" => $ordre++,
            ],
            [
                "code" =>  ParametreCategorie::VALIDATION_STAGE,
                "libelle" => 'Validation des stages',
                "ordre" => $ordre++,
            ],
            [
                "code" =>  ParametreCategorie::PROCEDURE_AFFECTATION,
                "libelle" => "Procédure d''affectation",
                "ordre" => $ordre++,
            ],
            [
                "code" => ParametreCategorie::MAIL,
                "libelle" => 'Mails automatiques',
                "ordre" => $ordre++,
            ],
            [
                "code" =>  ParametreCategorie::CONVENTION_STAGE,
                "libelle" => 'Conventions de stages',
                "ordre" => $ordre++,
            ],

            [
                "code" =>  ParametreCategorie::FOOTER,
                "libelle" => "Pied de pagede l'application",
                "ordre" => $ordre++,
            ],

            [
                "code" =>  ParametreCategorie::LOG,
                "libelle" => 'Logs applicatifs',
                "ordre" => $ordre++,
            ],
        ];
    }
    public function parametre_type() : array
    {
        $ordre = 1;
        return [
            [
                "code" => ParametreType::NO_TYPE,
                "libelle" => 'Non Spécifié',
                "cast_fonction" => null,
                "ordre" => $ordre++,
            ],
            [
                "code" => ParametreType::STRING,
                "libelle" => 'String',
                "cast_fonction" => null,
                "ordre" => $ordre++,
            ],
            [
                "code" => ParametreType::INT,
                "libelle" => 'Integer',
                "cast_fonction" => 'intval',
                "ordre" => $ordre++,
            ],
            [
                "code" => ParametreType::FLOAT,
                "libelle" => 'Float',
                "cast_fonction" => 'floatval',
                "ordre" => $ordre++,
            ],
            [
                "code" => ParametreType::BOOL,
                "libelle" => "Boolean",
                "cast_fonction" => 'boolval',
                "ordre" => $ordre++,
            ],
        ];
    }
    public function parametre() : array
    {
        $ordre = 1;
        return [
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DATE_CALCUL_ORDRES_AFFECTATIONS,
                "libelle" => "Date de calcul des ordres d'affectations",
                "description" => "Le calcul automatique des ordres d'affectations est effectué a priori x jours avant le début du stage",
                "value" => "48",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DATE_PHASE_CHOIX,
                "libelle" => "Date de la phase de définition des préférences",
                "description" => "La phase de choix commence a priori x jours avant le début du stage",
                "value" => "45",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DUREE_PHASE_CHOIX,
                "libelle" => "Durée de la phase de définition des préférences",
                "description" => "Durée a priori de la phase de choix",
                "value" => "15",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DATE_PHASE_AFFECTATION,
                "libelle" => "Date de la commission d'affectation",
                "description" => "La commission d'affectation a lieu a priori x jours avant le début du stage",
                "value" => "15",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DUREE_STAGE,
                "libelle" => "Durée d'un stage",
                "description" => "Durée a priori d'un stage",
                "value" => "30",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DATE_PHASE_VALIDATION,
                "libelle" => "Date de la phase de validation",
                "description" => "La phase de validation commence a priori x jours après le début du stage",
                "value" => "21",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DUREE_PHASE_VALIDATION,
                "libelle" => "Durée de la phase de validation",
                "description" => "Durée a priori de la phase de validation",
                "value" => "15",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DATES_PHASE_EVALUATION,
                "libelle" => "Date de la phase d'évaluation",
                "description" => "La phase d'évaluation commence a priori x jours après le début du stage",
                "value" => "31",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::DATE,
                "code" => Parametre::DUREE_PHASE_EVALUATION,
                "libelle" => "Durée de la phase d'évaluation",
                "description" => "Durée a priori de la phase d'évaluation",
                "value" => "15",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PREFERENCES,
                "code" => Parametre::NB_PREFERENCES,
                "libelle" => "Nombre de preference(s)",
                "description" => "Nombre de choix possible(s).",
                "value" => "12",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::VALIDATION_STAGE,
                "code" => Parametre::DUREE_TOKEN_VALDATION_STAGE,
                "libelle" => "Durée de vie des tokens",
                "description" => "Durée de vie des tokens de validations (en dehors de la phase de validation)",
                "value" => "15",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::PROCEDURE_AFFECTATION,
                "libelle" => "Procédure d'affectation",
                "description" => "Code de la procédure d'affectation utillisée",
                "value" => "algo_score_v2",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::PRECISION_COUT_AFFECTATION,
                "libelle" => "Précision des coûts",
                "description" => "Precision du cout d'une affectation",
                "value" => "2",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::COEF_INADEQUATION,
                "libelle" => "Coefficient d'inadéquation",
                "description" => "Coefficient d'inadéquation (en %) appliqué par l'algorithme de recommandation.",
                "value" => "10",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::AFFECTATION_COUT_TERRAIN_MAX,
                "libelle" => "Cout maximum d'un terrain de stage",
                "description" => "Borne max pour le coût par défaut d'un terrain de stage",
                "value" => "40",
                "parametre_type_id" => ParametreType::FLOAT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::FACTEUR_CORRECTEUR_COUT_TERRAIN,
                "libelle" => "Facteur correcteur - Cout des terrains",
                "description" => "Facteur correcteur appliqué lors du calcul du coûtd'un terrain de stage",
                "value" => "1.4",
                "parametre_type_id" => ParametreType::FLOAT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::FACTEUR_CORRECTEUR_BONUS_MALUS,
                "libelle" => "Facteur correcteur - Bonus/Malus",
                "description" => "Facteur correcteur appliqué lors du calcul du Bonus/Malus d'une affectation",
                "value" => "0.25",
                "parametre_type_id" => ParametreType::FLOAT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::PROCEDURE_AFFECTATION,
                "code" => Parametre::AFFECTATION_COUT_TOTAL_MAX,
                "libelle" => "Cout maximum d'une affectation",
                "description" => "Borne max pour le coût total d'une affectation de stage",
                "value" => "50",
                "parametre_type_id" => ParametreType::FLOAT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::MAIL,
                "code" => Parametre::DATE_PLANIFICATIONS_MAILS,
                "libelle" => "Dates de planification des mails",
                "description" => "Plannification des événements liés aux mails automatiques x jour avant leur date d'envoi.",
                "value" => "2",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::MAIL,
                "code" => Parametre::DELAI_RAPPELS,
                "libelle" => "Dates des mails de rappels",
                "description" => "Envoi automatique d'un mail de rappel x jours avant la date de fin d'une phase si nécessaire.",
                "value" => "1",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::ADRESSE_UFR_SANTE,
                "libelle" => "Adresse UFR sante",
                "description" => "Adresse de l'UFR de santé",
                "value" => isset($_ENV['UFR_ADDRESS']) && !empty($_ENV['UFR_ADDRESS']) ? $_ENV['UFR_ADDRESS'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::NOM_CHU,
                "libelle" => "Nom du CHU",
                "description" => "Nom du CHU",
                "value" => isset($_ENV['CHU_NAME']) && !empty($_ENV['CHU_NAME']) ? $_ENV['CHU_NAME'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::NOM_UFR_SANTE,
                "libelle" => "Nom de l'UFR",
                "description" => "Nom de l'UFR de santé",
                "value" => isset($_ENV['UFR_NAME']) && !empty($_ENV['UFR_NAME']) ? $_ENV['UFR_NAME'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::TELEPHONE_UFR_SANTE,
                "libelle" => "Tel UFR sante",
                "description" => "Téléphonne de l'UFR de santé",
                "value" => isset($_ENV['UFR_PHONE']) && !empty($_ENV['UFR_PHONE']) ? $_ENV['UFR_PHONE'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::FAX_UFR_SANTE,
                "libelle" => "Fax UFR sante",
                "description" => "Fax de l'UFR de santé",
                "value" => isset($_ENV['UFR_FAX']) && !empty($_ENV['UFR_FAX']) ? $_ENV['UFR_FAX'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::DOYEN_UFR_SANTE,
                "libelle" => "Doyen UFR sante",
                "description" => "Nom du doyen de l'UFR de santé",
                "value" => isset($_ENV['DOYEN']) && !empty($_ENV['DOYEN']) ? $_ENV['DOYEN'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::DIRECTEUR_CHU,
                "libelle" => "Directeur Général CHU",
                "description" => "Directeur Général du CHU",
                "value" => isset($_ENV['DIRECTOR']) && !empty($_ENV['DIRECTOR']) ? $_ENV['DIRECTOR'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::MAIL_UFR_SANTE,
                "libelle" => "Mail de l'UFR",
                "description" => "Mail de contact à l'UFR de santé",
                "value" => isset($_ENV['UFR_MAIL']) && !empty($_ENV['UFR_MAIL']) ? $_ENV['UFR_MAIL'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::CONVENTION_STAGE,
                "code" => Parametre::DUREE_CONSERVCATION,
                "libelle" => "Durée de conservation",
                "description" => "Durée de conservation des conventions de stages (en jours)",
                "value" => "724",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::FOOTER,
                "code" => Parametre::FOOTER_UNIV_NAME,
                "libelle" => "Nom de l'Université",
                "description" => "Nom de l'Université",
                "value" => isset($_ENV['UNIV_NAME']) && !empty($_ENV['UNIV_NAME']) ? $_ENV['UNIV_NAME'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::FOOTER,
                "code" => Parametre::FOOTER_UNIV_URL,
                "libelle" => "Site de l'Université",
                "description" => "URL vers le site l'université qui apparait en pied de page",
                "value" => isset($_ENV['UNIV_WEBSITE']) && !empty($_ENV['UNIV_WEBSITE']) ? $_ENV['UNIV_WEBSITE'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::FOOTER,
                "code" => Parametre::FOOTER_UNIV_LOGO,
                "libelle" => "Logo de l'université",
                "description" => "URL du logo de l'université mis en pied de page",
                "value" => isset($_ENV['UNIV_LOGO_URL']) && !empty($_ENV['UNIV_LOGO_URL']) ? $_ENV['UNIV_LOGO_URL'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::FOOTER,
                "code" => Parametre::FOOTER_UNIV_CONTACT,
                "libelle" => "Liens vers la page 'Contact'",
                "description" => "URL vers la page Contact (de l'Université, a ne pas confondre avec les contacts interne à l'application)",
                "value" => isset($_ENV['UNIV_CONTACT_URL']) && !empty($_ENV['UNIV_CONTACT_URL']) ? $_ENV['UNIV_CONTACT_URL'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::FOOTER,
                "code" => Parametre::FOOTER_UNIV_MENTIONS_LEGALS,
                "libelle" => "Liens vers la page 'Mention l'égales'",
                "description" => "URL vers la page Mention l'égales",
                "value" => isset($_ENV['UNIV_LEGAL_URL']) && !empty($_ENV['UNIV_LEGAL_URL']) ? $_ENV['UNIV_LEGAL_URL'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::FOOTER,
                "code" => Parametre::FOOTER_UNIV_VIE_PRIVEE,
                "libelle" => "Liens vers la page 'Vie Privée'",
                "description" => "URL vers la page Vie Privée",
                "value" => isset($_ENV['UNIV_PRIVACY_URL']) && !empty($_ENV['UNIV_PRIVACY_URL']) ? $_ENV['UNIV_PRIVACY_URL'] : "[A renseigner]",
                "parametre_type_id" => ParametreType::STRING,
                "ordre" => $ordre++,
            ],

            [
                "categorie_id" => ParametreCategorie::LOG,
                "code" => Parametre::CONSERVATION_LOG,
                "libelle" => "Conservation des logs",
                "description" => "Nombre de jours de conservation des logs.",
                "value" => "365",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::LOG,
                "code" => Parametre::CONSERVATION_MAIL,
                "libelle" => "conservation des mails",
                "description" => "Nombre de jours de conservation des mails.",
                "value" => "60",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
            [
                "categorie_id" => ParametreCategorie::LOG,
                "code" => Parametre::CONSERVATION_EVENEMENT,
                "libelle" => "Conservation des événements",
                "description" => "Nombre de jours de conservation des événements.",
                "value" => "60",
                "parametre_type_id" => ParametreType::INT,
                "ordre" => $ordre++,
            ],
        ];
    }


    public function parametre_cout_affectation() : array
    {
        return [
            ["rang" => 1, "cout" => 5,],
            ["rang" => 2, "cout" => 4,],
            ["rang" => 3, "cout" => 3,],
            ["rang" => 4, "cout" => 3,],
            ["rang" => 5, "cout" => 2,],
            ["rang" => 6, "cout" => 2,],
            ["rang" => 7, "cout" => 2,],
            ["rang" => 8, "cout" => 1,],
            ["rang" => 9, "cout" => 1,],
            ["rang" => 10, "cout" => 1,],
            ["rang" => 11, "cout" => 1,],
            ["rang" => 12, "cout" => 1,],
        ];

    }
    public function unicaen_parametre_categorie() : array
    {
        $ordre = 1;
        return [
//            [
//                "code" => ParametreFiliere::CODE_CATEGORIE_PARAMETRE,
//                "libelle" => "Filières en santé",
//                "description" => "Paramétres des filières en santé",
//                "ordre" => $ordre++,
//            ],
        ];
    }
    public function unicaen_parametre_parametre() : array
    {
        $ordre = 1;
        return [
//            [//Pseudo paramétre car sa valeur est dépendantes de la filière, de l'année et du groupe de niveau, cf parametre_filiere
//                "categorie_id" =>  ParametreFiliere::CODE_CATEGORIE_PARAMETRE,
//                "code" =>  ParametreFiliere::CODE_PARAMETRE_NB_PLACES,
//                "libelle" => "Nombres de places",
//                "description" => "Nombres de places disponibles dans une filière",
//                'valeurs_possibles' => 'Number',
//                'valeur' => '100',
//                'ordre' => $ordre++,
//                'modifiable' => true,
//                'affichable' => true,
//            ],
        ];
    }




}