<?php

namespace BddAdmin\Data;


use Application\Provider\EtatType\AffectationEtatTypeProvider;
use Application\Provider\EtatType\AnneeEtatTypeProvider;
use Application\Provider\EtatType\ContrainteCursusEtudiantEtatTypeProvider;
use Application\Provider\EtatType\EtudiantEtatTypeProvider;
use Application\Provider\EtatType\SessionEtatTypeProvider;
use Application\Provider\EtatType\StageEtatTypeProvider;
use Application\Provider\EtatType\ValidationStageEtatTypeProvider;
use Application\Provider\Misc\Color;
use Application\Provider\Misc\Icone;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenEtatDataProvider implements DataProviderInterface {

    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_etat_categorie' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
                ];
                break;
            case 'unicaen_etat_type' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update'  => true, 'soft-delete' => false, 'delete' => false,
                        'columns' => ['categorie_id' => ['transformer' => 'select id from unicaen_etat_categorie where code = %s'],
                        ]]
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }




    public function unicaen_etat_categorie() : array
    {
        $ordre = 1;
        return [
            [
                "code" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Étudiant",
                "icone" => Icone::ETUDIANT,
                "couleur" => COLOR::DARK_BLUE,
                "ordre" => $ordre++,
            ],
            [
                "code" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Année",
                "icone" => Icone::ANNEE,
                "couleur" => COLOR::DARK_BLUE,
                "ordre" => $ordre++,
            ],
            [
                "code" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Session de stage",
                "icone" => Icone::SESSION_STAGE,
                "couleur" => COLOR::INFO,
                "ordre" => $ordre++,
            ],
            [
                "code" => StageEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Stage",
                "icone" => Icone::STAGE,
                "couleur" => COLOR::INFO,
                "ordre" => $ordre++,
            ],
            [
                "code" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Affectation",
                "icone" => Icone::AFFECTATION,
                "couleur" => COLOR::INFO,
                "ordre" => $ordre++,
            ],
            [
                "code" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Validation des stages",
                "icone" => Icone::FA_DOUBLE_CHECK,
                "couleur" => COLOR::SUCCESS,
                "ordre" => $ordre++,
            ],
            [
                "code" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Contraintes de cursus",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::WARNING,
                "ordre" => $ordre++,
            ],
        ];
    }


    //Choix fait de subdiviser les etats par catégories pour simplifier les modifications
    public function unicaen_etat_type() : array
    {
        return array_merge(
            $this->getEtatsEtudiant(),
            $this->getEtatsAnnee(),
            $this->getEtatsSessionStage(),
            $this->getEtatsStage(),
            $this->getEtatsAffectation(),
            $this->getEtatsValidationStage(),
            $this->getEtatsContrainte(),
        );
    }

    protected function getEtatsEtudiant() : array
    {
        $ordre = 1;
        return [
            [
                "code" => "etudiant_cursus_en_cours",
                "libelle" => "Cursus en cours",
                "icone" => Icone::EN_COURS,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_cursus_valide",
                "libelle" => "Cursus terminé - Validé",
                "icone" => Icone::VALIDE,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_cursus_invalide",
                "libelle" => "Cursus terminé - Non validé",
                "icone" => Icone::NON_VALIDE,
                "couleur" => COLOR::DANGER,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_en_alerte",
                "libelle" => "Cursus à surveiller",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_en_erreur",
                "libelle" => "Erreur dans le cursus",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::DANGER,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_dispo",
                "libelle" => "En disponibilité",
                "icone" => Icone::EN_PAUSE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_en_construction",
                "libelle" => "Cursus en construction",
                "icone" => Icone::FA_COGS,
                "couleur" => COLOR::INFO,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
        ];
    }

    protected function getEtatsAnnee() : array
    {
        $ordre = 1;
        return [
            [
                "code" => "annee_en_construction",
                "libelle" => "En construction",
                "icone" => Icone::FA_COGS,
                "couleur" => COLOR::INFO,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_non_valide",
                "libelle" => "Non validée",
                "icone" => Icone::FA_UNLOCK,
                "couleur" => COLOR::WARNING,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_en_construction_retard",
                "libelle" => "En construction - En retard",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::WARNING,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_futur",
                "libelle" => "Future",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::INFO,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_en_cours",
                "libelle" => "En cours",
                "icone" => Icone::EN_COURS,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_termine",
                "libelle" => "Terminée",
                "icone" => Icone::TERMINE,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
        ];
    }

    protected function getEtatsSessionStage() : array
    {
        $ordre = 1;
        return [
            [
                "code" => "session_futur",
                "libelle" => "Future",
                "icone" => Icone::FUTUR,
                "couleur" => COLOR::INFO,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_preference",
                "libelle" => "Phase de définition des préférences",
                "icone" => Icone::FA_CHECK_LIST,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_affectation",
                "libelle" => "Phase d'affectation",
                "icone" => Icone::CHOIX,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_a_venir",
                "libelle" => "Début des stages à venir",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_en_cours",
                "libelle" => "Stages en cours",
                "icone" => Icone::EN_COURS,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_validation",
                "libelle" => "Phase de validation",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::LIGHT_GREEN,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_evaluation",
                "libelle" => "Phase d'évalutaion",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::INFO,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_termine",
                "libelle" => "Session terminée",
                "icone" => Icone::FA_CHECK,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_en_alerte",
                "libelle" => "Session en alerte",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_en_erreur",
                "libelle" => "Session en erreur",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::DANGER,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_desactive",
                "libelle" => "Session désactivée",
                "icone" => Icone::ANNULE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
        ];
    }

    protected function getEtatsStage() : array
    {
        $ordre = 1;
        return [
            [
                "code" => "stage_futur",
                "libelle" => "Future",
                "icone" => Icone::FUTUR,
                "couleur" => COLOR::INFO,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_preference",
                "libelle" => "Phase de définition des préférences",
                "icone" => Icone::FA_CHECK_LIST,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_affectation",
                "libelle" => "En cours d'attribution",
                "icone" => Icone::CHOIX,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_a_venir",
                "libelle" => "Début du stage à venir",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_cours",
                "libelle" => "Stage en cours",
                "icone" => Icone::EN_COURS,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_validation",
                "libelle" => "En attente de validation",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::LIGHT_GREEN,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_validation_retard",
                "libelle" => "Validation non effectuée",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::WARNING,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_evaluation",
                "libelle" => "En attente d'une évalutaion",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::INFO,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_evaluation_retard",
                "libelle" => "Évalutaion non effectuée",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::WARNING,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_termine_valide",
                "libelle" => "Stage validé",
                "icone" => Icone::FA_CHECK,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_termine_non_valide",
                "libelle" => "Stage non validé",
                "icone" => Icone::NON_VALIDE,
                "couleur" => COLOR::DANGER,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_alerte",
                "libelle" => "Stage en alerte",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_erreur",
                "libelle" => "Stage en erreur",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::DANGER,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_non_effectue",
                "libelle" => "Stage non effectué",
                "icone" => Icone::FA_BAN,
                "couleur" => COLOR::MUTED,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_disponibilite",
                "libelle" => "Étudiant en disponibilité",
                "icone" => Icone::EN_PAUSE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_desactive",
                "libelle" => "Stage désactivé",
                "icone" => Icone::ANNULE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
        ];
    }

    protected function getEtatsAffectation() : array
    {
        $ordre = 1;
        return [

            [
                "code" => "affectation_futur",
                "libelle" => "Future",
                "icone" => Icone::FUTUR,
                "couleur" => COLOR::MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_cours",
                "libelle" => "Affectation en cours",
                "icone" => Icone::EN_COURS,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_retard",
                "libelle" => "En attente d'affectation",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::WARNING,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_proposition",
                "libelle" => "Proposition d'affectation",
                "icone" => "far fa-circle-up",
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_pre_valide",
                "libelle" => "Pré-validée",
                "icone" => Icone::FA_CHECK,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_valide",
                "libelle" => "Validée",
                "icone" => Icone::FA_DOUBLE_CHECK,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_alerte",
                "libelle" => "Affectation en alerte",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_erreur",
                "libelle" => "Affectation en erreur",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::DANGER,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_stage_non_effectue",
                "libelle" => "Stage non effectué",
                "icone" => Icone::FA_BAN,
                "couleur" => COLOR::MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_disponibilite",
                "libelle" => "Étudiant en disponibilité",
                "icone" => Icone::EN_PAUSE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_non_affecte",
                "libelle" => "Non affecté",
                "icone" => Icone::ANNULE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
        ];
    }
    protected function getEtatsContrainte() : array
    {
        $ordre = 1;
        return [
            [
                "code" => "validation_stage_valide",
                "libelle" => "Stage validé",
                "icone" => Icone::FA_DOUBLE_CHECK,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_invalide",
                "libelle" => "Stage non validé",
                "icone" => Icone::NON_VALIDE,
                "couleur" => COLOR::DANGER,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_futur",
                "libelle" => "Future",
                "icone" => Icone::FUTUR,
                "couleur" => COLOR::MUTED,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_attente",
                "libelle" => "En attente de validation",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::DARK_BLUE,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_retard",
                "libelle" => "Validation non effectuée",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::WARNING,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_stage_non_effectue",
                "libelle" => "Stage non effectué",
                "icone" => Icone::FA_BAN,
                "couleur" => COLOR::MUTED,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_disponibilite",
                "libelle" => "Étudiant en disponibilité",
                "icone" => Icone::EN_PAUSE,
                "couleur" => COLOR::MUTED,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_alerte",
                "libelle" => "Validation en alerte",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_erreur",
                "libelle" => "Validation en erreur",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::DANGER,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
        ];
    }
    protected function getEtatsValidationStage() : array
    {
        $ordre = 1;
        return [
            [
                "code" => "contrainte_cursus_sat",
                "libelle" => "Satisfaite",
                "icone" => Icone::FA_CHECK,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_valide",
                "libelle" => "Validée par la commission",
                "icone" => Icone::FA_CHECK_SQUARE,
                "couleur" => COLOR::SUCCESS,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_non_sat",
                "libelle" => "Non satisfaite",
                "icone" => Icone::EN_ATTENTE,
                "couleur" => COLOR::PRIMARY,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_warning",
                "libelle" => "À surveiller",
                "icone" => Icone::WARNING,
                "couleur" => COLOR::WARNING,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_insat",
                "libelle" => "Insatifiable",
                "icone" => Icone::FA_EXCLATION_CIRCLE,
                "couleur" => COLOR::DANGER,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_en_erreur",
                "libelle" => "En erreur",
                "icone" => Icone::ERROR,
                "couleur" => COLOR::DANGER,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_desactive",
                "libelle" => "Désactivé",
                "icone" => Icone::FA_BAN,
                "couleur" => COLOR::MUTED,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],

        ];
    }
}