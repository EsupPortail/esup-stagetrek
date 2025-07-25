<?php

namespace BddAdmin\Data;


use Application\Provider\Etats\EtatCandidature;
use Application\Provider\Etats\EtatCandidatureChoixFiliere;
use Application\Provider\Etats\EtatChoixPostGroupeEpreuve;
use Application\Provider\Etats\EtatChoixPostGroupeEpreuveFiliere;
use Application\Provider\Etats\EtatConfirmationCandidature;
use Application\Provider\Etats\EtatFormation;
use Application\Provider\Etats\EtatPreCandidature;
use Application\Provider\Etats\EtatPreparationSecondGroupe;
use Application\Provider\Etats\EtatRecevabilitePreCandidature;
use Application\Provider\Etats\EtatResultatGroupeEpreuve;
use Application\Provider\Etats\EtatSecondGroupeEpreuve;
use Application\Provider\Etats\EtatUe;
use Application\Provider\EtatType\AffectationEtatTypeProvider;
use Application\Provider\EtatType\AnneeEtatTypeProvider;
use Application\Provider\EtatType\ContrainteCursusEtudiantEtatTypeProvider;
use Application\Provider\EtatType\EtudiantEtatTypeProvider;
use Application\Provider\EtatType\SessionEtatTypeProvider;
use Application\Provider\EtatType\StageEtatTypeProvider;
use Application\Provider\EtatType\ValidationStageEtatTypeProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenEtatDataProvider implements DataProviderInterface {

    CONST COUlEUR_DARK_BLUE = "#204a87";
    CONST COUlEUR_PRIMARY = "#0D6EFD";
    CONST COUlEUR_SUCCESS = "#006400";
    CONST COUlEUR_LIGHT_GREEN = "#04AA6D";
    CONST COUlEUR_INFO = "#729FCF";
    CONST COUlEUR_DANGER = "#C80000";
    CONST COUlEUR_WARNING = "#EE6622";
    CONST COUlEUR_MUTED = "#A0A0A0";
    CONST COUlEUR_DARK_GRAY = "#888a85";
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

    const ICONE_AWARD = "fas fa-award";
    const ICONE_CERTIFCATE = "fas fa-certificate";
    const ICONE_PROCESS = "fas fa-gears";
    const ICONE_CHECK = "fas fa-check";
    const ICONE_CHECK_LIST = "fas fa-list-check";
    const ICONE_SQUARE_CHECK = "fas fa-square-check";
    const ICONE_DOUBLE_CHECK = "fas fa-check-double";
    const ICONE_PROGRESS_BAR ="fas fa-bars-progress";
    const ICONE_FA1 = "fas fa-1";
    const ICONE_FA2 = "fas fa-2";
    const ICONE_SAVE = "fas fa-save";
    const ICONE_USER = "fas fa-user";
    const ICONE_LOCK = "fas fa-lock";
    const ICONE_UNLOCK = "fas fa-lock-open";
    const ICONE_SESSION_STAGE = "fas fa-briefcase-medical";
    const ICONE_STAGE = "fas fa-notes-medical";
    const ICONE_ANNEE = "fas fa-calendar";
    const ICONE_AFFECTATION = "fas fa-flag";
    const ICONE_VOTE = "fas fa-check-to-slot";


    const ICONE_ETAT_VALIDE = self::ICONE_CHECK;
    const ICONE_ETAT_PUBLIE = self::ICONE_CHECK;
    const ICONE_ETAT_SAVE = self::ICONE_SAVE;
    const ICONE_ETAT_CONSTRUCTION = "fas fa-cogs";

    const ICONE_ETAT_NON_AUTORISE= "fas fa-ban";
    const ICONE_ETAT_NON_VALIDE = "fas fa-times";
    const ICONE_ETAT_NON_ADMIS = "fas fa-times";
    const ICONE_ETAT_NON_EFFECTUE = "fas fa-ban";
    const ICONE_ETAT_REFUSE= "fas fa-circle-xmark";
    const ICONE_ETAT_ANNULE= "fas fa-circle-xmark";
    const ICONE_ETAT_NON_PUBLIE= "fas fa-circle-xmark";

    const ICONE_ETAT_FUTUR = "fas fa-clock";
    const ICONE_ETAT_EN_COURS = "fas fa-play";
    const ICONE_ETAT_EN_ATTENTE = "fas fa-hourglass";
    const ICONE_ETAT_EN_PAUSE = "fas fa-pause";
    const ICONE_ETAT_INDETERMINE = "fas fa-circle-question";
    const ICONE_ETAT_NON_DEFINI = "fas fa-spinner";
    const ICONE_ETAT_TERMINE = "fas fa-check";
    const ICONE_ETAT_WARNING = "fas fa-exclamation-triangle";
    const ICONE_ETAT_EN_ERREUR = "fas fa-exclamation-triangle";
    const ICONE_ETAT_INSAT = "fas fa-exclamation-circle";

    public function unicaen_etat_categorie() : array
    {
        $ordre = 1;
        return [
            [
                "code" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Étudiant",
                "icone" => self::ICONE_USER,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "ordre" => $ordre++,
            ],
            [
                "code" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Année",
                "icone" => self::ICONE_ANNEE,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "ordre" => $ordre++,
            ],
            [
                "code" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Session de stage",
                "icone" => self::ICONE_SESSION_STAGE,
                "couleur" => self::COUlEUR_INFO,
                "ordre" => $ordre++,
            ],
            [
                "code" => StageEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Stage",
                "icone" => self::ICONE_STAGE,
                "couleur" => self::COUlEUR_INFO,
                "ordre" => $ordre++,
            ],
            [
                "code" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Affectation",
                "icone" => self::ICONE_AFFECTATION,
                "couleur" => self::COUlEUR_INFO,
                "ordre" => $ordre++,
            ],
            [
                "code" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Validation des stages",
                "icone" => self::ICONE_DOUBLE_CHECK,
                "couleur" => self::COUlEUR_SUCCESS,
                "ordre" => $ordre++,
            ],
            [
                "code" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "libelle" => "Contraintes de cursus",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_WARNING,
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
                "icone" => self::ICONE_ETAT_EN_COURS,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_cursus_valide",
                "libelle" => "Cursus terminé - Validé",
                "icone" => self::ICONE_ETAT_VALIDE,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_cursus_invalide",
                "libelle" => "Cursus terminé - Non validé",
                "icone" => self::ICONE_ETAT_NON_VALIDE,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_en_alerte",
                "libelle" => "Cursus à surveiller",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_en_erreur",
                "libelle" => "Erreur dans le cursus",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_dispo",
                "libelle" => "En disponibilité",
                "icone" => self::ICONE_ETAT_EN_PAUSE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => EtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "etudiant_en_construction",
                "libelle" => "Cursus en construction",
                "icone" => self::ICONE_ETAT_CONSTRUCTION,
                "couleur" => self::COUlEUR_INFO,
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
                "icone" => self::ICONE_ETAT_CONSTRUCTION,
                "couleur" => self::COUlEUR_INFO,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_non_valide",
                "libelle" => "Non validée",
                "icone" => self::ICONE_UNLOCK,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_en_construction_retard",
                "libelle" => "En construction - En retard",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_futur",
                "libelle" => "Future",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_INFO,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_en_cours",
                "libelle" => "En cours",
                "icone" => self::ICONE_ETAT_EN_COURS,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => AnneeEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "annee_termine",
                "libelle" => "Terminée",
                "icone" => self::ICONE_ETAT_TERMINE,
                "couleur" => self::COUlEUR_SUCCESS,
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
                "icone" => self::ICONE_ETAT_FUTUR,
                "couleur" => self::COUlEUR_INFO,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_preference",
                "libelle" => "Phase de définition des préférences",
                "icone" => self::ICONE_CHECK_LIST,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_affectation",
                "libelle" => "Phase d'affectation",
                "icone" => self::ICONE_VOTE,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_a_venir",
                "libelle" => "Début des stages à venir",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_en_cours",
                "libelle" => "Stages en cours",
                "icone" => self::ICONE_ETAT_EN_COURS,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_validation",
                "libelle" => "Phase de validation",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_LIGHT_GREEN,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_evaluation",
                "libelle" => "Phase d'évalutaion",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_INFO,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_termine",
                "libelle" => "Session terminée",
                "icone" => self::ICONE_CHECK,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_en_alerte",
                "libelle" => "Session en alerte",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_en_erreur",
                "libelle" => "Session en erreur",
                "icone" => self::ICONE_ETAT_EN_ERREUR,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => SessionEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "session_desactive",
                "libelle" => "Session désactivée",
                "icone" => self::ICONE_ETAT_ANNULE,
                "couleur" => self::COUlEUR_MUTED,
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
                "icone" => self::ICONE_ETAT_FUTUR,
                "couleur" => self::COUlEUR_INFO,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_preference",
                "libelle" => "Phase de définition des préférences",
                "icone" => self::ICONE_CHECK_LIST,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_affectation",
                "libelle" => "En cours d'attribution",
                "icone" => self::ICONE_VOTE,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_a_venir",
                "libelle" => "Début du stage à venir",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_cours",
                "libelle" => "Stage en cours",
                "icone" => self::ICONE_ETAT_EN_COURS,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_validation",
                "libelle" => "En attente de validation",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_LIGHT_GREEN,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_validation_retard",
                "libelle" => "Validation non effectuée",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_evaluation",
                "libelle" => "En attente d'une évalutaion",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_INFO,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_evaluation_retard",
                "libelle" => "Évalutaion non effectuée",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_termine_valide",
                "libelle" => "Stage validé",
                "icone" => self::ICONE_ETAT_VALIDE,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_termine_non_valide",
                "libelle" => "Stage non validé",
                "icone" => self::ICONE_ETAT_NON_VALIDE,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_alerte",
                "libelle" => "Stage en alerte",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_erreur",
                "libelle" => "Stage en erreur",
                "icone" => self::ICONE_ETAT_EN_ERREUR,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_non_effectue",
                "libelle" => "Stage non effectué",
                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_en_disponibilite",
                "libelle" => "Étudiant en disponibilité",
                "icone" => self::ICONE_ETAT_EN_PAUSE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => StageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "stage_desactive",
                "libelle" => "Stage désactivé",
                "icone" => self::ICONE_ETAT_ANNULE,
                "couleur" => self::COUlEUR_MUTED,
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
                "icone" => self::ICONE_ETAT_FUTUR,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_cours",
                "libelle" => "Affectation en cours",
                "icone" => self::ICONE_ETAT_EN_COURS,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_retard",
                "libelle" => "En attente d'affectation",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_proposition",
                "libelle" => "Proposition d'affectation",
                "icone" => "far fa-circle-up",
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_pre_valide",
                "libelle" => "Pré-validée",
                "icone" => self::ICONE_CHECK,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_valide",
                "libelle" => "Validée",
                "icone" => self::ICONE_DOUBLE_CHECK,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_alerte",
                "libelle" => "Affectation en alerte",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_erreur",
                "libelle" => "Affectation en erreur",
                "icone" => self::ICONE_ETAT_EN_ERREUR,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_stage_non_effectue",
                "libelle" => "Stage non effectué",
                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_en_disponibilite",
                "libelle" => "Étudiant en disponibilité",
                "icone" => self::ICONE_ETAT_EN_PAUSE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => AffectationEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "affectation_non_affecte",
                "libelle" => "Non affecté",
                "icone" => self::ICONE_ETAT_ANNULE,
                "couleur" => self::COUlEUR_MUTED,
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
                "icone" => self::ICONE_DOUBLE_CHECK,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_invalide",
                "libelle" => "Stage non validé",
                "icone" => self::ICONE_ETAT_NON_VALIDE,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_futur",
                "libelle" => "Future",
                "icone" => self::ICONE_ETAT_FUTUR,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_attente",
                "libelle" => "En attente de validation",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_DARK_BLUE,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_retard",
                "libelle" => "Validation non effectuée",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_stage_non_effectue",
                "libelle" => "Stage non effectué",
                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_disponibilite",
                "libelle" => "Étudiant en disponibilité",
                "icone" => self::ICONE_ETAT_EN_PAUSE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_alerte",
                "libelle" => "Validation en alerte",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => ValidationStageEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "validation_stage_en_erreur",
                "libelle" => "Validation en erreur",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_DANGER,
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
                "icone" => self::ICONE_CHECK,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_valide",
                "libelle" => "Validée par la commission",
                "icone" => self::ICONE_SQUARE_CHECK,
                "couleur" => self::COUlEUR_SUCCESS,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_non_sat",
                "libelle" => "Non satisfaite",
                "icone" => self::ICONE_ETAT_EN_ATTENTE,
                "couleur" => self::COUlEUR_PRIMARY,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_warning",
                "libelle" => "À surveiller",
                "icone" => self::ICONE_ETAT_WARNING,
                "couleur" => self::COUlEUR_WARNING,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_insat",
                "libelle" => "Insatifiable",
                "icone" => self::ICONE_ETAT_INSAT,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_en_erreur",
                "libelle" => "En erreur",
                "icone" => self::ICONE_ETAT_EN_ERREUR,
                "couleur" => self::COUlEUR_DANGER,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],
            [
                "code" => "contrainte_cursus_desactive",
                "libelle" => "Désactivé",
                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
                "couleur" => self::COUlEUR_MUTED,
                "categorie_id" => ContrainteCursusEtudiantEtatTypeProvider::CODE_CATEGORIE,
                "ordre" => $ordre++,
            ],

        ];
    }

//    protected function getEtatUe() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "ue_validee",
//                "libelle" => "Validée",
//                "icone" => self::ICONE_ETAT_VALIDE,
//                "couleur" => self::COUlEUR_LIGHT_GREEN,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatUe::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "ue_non_validee",
//                "libelle" => "Non Validée",
//                "icone" => self::ICONE_ETAT_NON_VALIDE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatUe::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "ue_validation_non_definie",
//                "libelle" => "Validation non définie",
//                "icone" => self::ICONE_ETAT_NON_DEFINI,
//                "couleur" => self::COUlEUR_DARK_GRAY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatUe::CODE_CATEGORIE,
//            ],
//        ];
//    }
//
//    protected function getEtatCandidature() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "candidature_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_affecte_maieutique",
//                "libelle" => "Affecté en maïeutique",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_affecte_medecine",
//                "libelle" => "Affecté en médecine",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_affecte_odonto",
//                "libelle" => "Affecté en odontologie",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_affecte_pharma",
//                "libelle" => "Affecté en pharmacie",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_affecte_kine",
//                "libelle" => "Affecté en Masso-kinésithérapie",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_non_admis",
//                "libelle" => "Non admis",
//                "icone" => self::ICONE_ETAT_NON_ADMIS,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_inscrit_2nd_groupe",
//                "libelle" => "Accés à l'oral confirmé",
//                "icone" => self::ICONE_FA2,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_liste_attente",
//                "libelle" => "Sur liste d'attente",
//                "icone" => self::ICONE_PROGRESS_BAR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_abandon",
//                "libelle" => "Affectation refusée",
//                "icone" =>  self::ICONE_ETAT_REFUSE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_enregistre",
//                "libelle" => "Énregistrée",
//                "icone" =>  self::ICONE_ETAT_SAVE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_pre_candidature_recevable",
//                "libelle" => "Pré-candidature recevable",
//                "icone" => self::ICONE_ETAT_VALIDE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_recevabilite_futur",
//                "libelle" => "Résultat de recevabilité à venir",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_recevabilite_retard",
//                "libelle" => "Résultat de recevabilité non publiée",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_pre_candidature_en_cours",
//                "libelle" => "Déclaration de pré-candidature en cours",
//                "icone" => self::ICONE_ETAT_EN_COURS,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_pre_candidature_valide",
//                "libelle" => "Pré-candidature enregistrée",
//                "icone" =>  self::ICONE_ETAT_SAVE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_pre_candidature_refuse",
//                "libelle" => "Déclaration de candidature refusée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_en_attente_pre_candidature",
//                "libelle" => "En attente de la déclaration de pré-candidature",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_non_confirme",
//                "libelle" => "Candidature non confirmée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_en_attente_choix_post_groupe",
//                "libelle" => "En attente de choix",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_choix_post_groupe_non_effectue",
//                "libelle" => "Choix post premier groupes d'épreuves non-éffectués",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "candidature_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidature::CODE_CATEGORIE,
//            ],
//        ];
//    }
//
//    protected function getEtatPreCandidature() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "pre_candidature_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_enregistre",
//                "libelle" => "Enregistrées",
//                "icone" =>  self::ICONE_ETAT_SAVE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_refuse",
//                "libelle" => "Déclaration refusée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_en_attente_declaration",
//                "libelle" => "En attente de déclaration",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_en_attente_validation",
//                "libelle" => "Én attente de validation par un responsable administratif",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "pre_candidature_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreCandidature::CODE_CATEGORIE,
//            ],
//        ];
//    }
//
//
//    protected function getEtatRecevabilitePreCandidature() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "recevabilite_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_recevable",
//                "libelle" => "Pré-candidature recevable",
//                "icone" => self::ICONE_ETAT_VALIDE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_non_recevable",
//                "libelle" => "Pré-candidature non recevable",
//                "icone" => self::ICONE_ETAT_NON_ADMIS,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_non_publiee",
//                "libelle" => "Non publiée",
//                "icone" => self::ICONE_ETAT_NON_PUBLIE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "recevabilite_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatRecevabilitePreCandidature::CODE_CATEGORIE,
//            ],
//
//        ];
//    }
//
//    protected function getEtatCandidatureChoixFiliere() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "choix_filieres_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_en_attente_choix",
//                "libelle" => "En attente de choix",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_enregistre",
//                "libelle" => "Enregistrées et Confirmées",
//                "icone" =>  self::ICONE_ETAT_SAVE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_en_attente_confirmation",
//                "libelle" => "En attente de confirmation",
//                "icone" =>  self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_non_confirme",
//                "libelle" => "Choix non confirmés",
//                "icone" =>  self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_filieres_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatCandidatureChoixFiliere::CODE_CATEGORIE,
//            ],];
//    }
//
//    protected function getEtatConfirmationCandidature() : array
//    {
//        $ordre = 1;
//        return [
//
//            [
//                "code" => "confirmation_candidature_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_en_attente_choix",
//                "libelle" => "En attente de choix",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_en_attente_confirmation",
//                "libelle" => "En attente de confirmation",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_confirme",
//                "libelle" => "Candidature confirmée",
//                "icone" =>  self::ICONE_ETAT_SAVE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_non_confirme",
//                "libelle" => "Candidature non confirmées",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "confirmation_candidature_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatConfirmationCandidature::CODE_CATEGORIE,
//            ],
//        ];
//    }
//
//    protected function getEtatCandidatureResultatGroupeEpreuve1() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "resultat_groupe_epreuve_1_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_admission_direct",
//                "libelle" => "Admis",
//                "icone" => self::ICONE_ETAT_VALIDE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_admission_indirect",
//                "libelle" => "Admissible au second groupe d'épreuves",
//                "icone" => self::ICONE_ETAT_VALIDE,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_non_admis",
//                "libelle" => "Non admis",
//                "icone" => self::ICONE_ETAT_NON_ADMIS,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_liste_attente",
//                "libelle" => "Liste d'attente",
//                "icone" => self::ICONE_PROGRESS_BAR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_publie",
//                "libelle" => "Publiés",
//                "icone" => self::ICONE_ETAT_PUBLIE,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_non_publie",
//                "libelle" => "Non publié",
//                "icone" => self::ICONE_ETAT_NON_PUBLIE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "resultat_groupe_epreuve_1_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatResultatGroupeEpreuve::CODE_CATEGORIE,
//            ],
//        ];
//    }
//    protected function getEtatChoixPostGroupeEpreuve() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "choix_post_groupe_epreuve_futur",
//                "libelle" => "Futur",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_en_attente_choix",
//                "libelle" => "En attente de choix",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_admission_confirmee",
//                "libelle" => "Admission confirmée",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_2nd_groupe_confirme",
//                "libelle" => "Accés à l'oral confirmé",
//                "icone" => self::ICONE_FA2,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_liste_attente",
//                "libelle" => "Sur liste d'attente",
//                "icone" => self::ICONE_PROGRESS_BAR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_non_admis",
//                "libelle" => "Non admis",
//                "icone" => self::ICONE_ETAT_NON_ADMIS,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_abandon",
//                "libelle" => "Affectation refusée",
//                "icone" => self::ICONE_ETAT_REFUSE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_choix_non_effecutes",
//                "libelle" => "Choix non effectués",
//                "icone" => self::ICONE_ETAT_EN_ATTENTE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_non_autorise",
//                "libelle" => "Candidature non autorisée",
//                "icone" => self::ICONE_ETAT_NON_AUTORISE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_non_effectue",
//                "libelle" => "Candidature non effectuée",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_annule",
//                "libelle" => "Candidature annulée",
//                "icone" => self::ICONE_ETAT_ANNULE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuve::CODE_CATEGORIE,
//            ],
//        ];
//    }
//
//    protected function getEtatChoixPostGroupeEpreuveFiliere() : array
//    {
//        $ordre = 1;
//        return [
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_affecte",
//                "libelle" => "Affectation confirmée",
//                "icone" => self::ICONE_DOUBLE_CHECK,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_2nd_groupe",
//                "libelle" => "Accés à l'oral confirmé",
//                "icone" => self::ICONE_FA2,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_liste_attente",
//                "libelle" => "Sur liste d'attente",
//                "icone" => self::ICONE_PROGRESS_BAR,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_reporte",
//                "libelle" => "Classement reporté",
//                "icone" => self::ICONE_FA1,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_refuse",
//                "libelle" => "Affectation refusée",
//                "icone" => self::ICONE_ETAT_REFUSE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_non_admis",
//                "libelle" => "Non admis",
//                "icone" => self::ICONE_ETAT_NON_ADMIS,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_desactive",
//                "libelle" => "Acceptation d'une autre filière",
//                "icone" => self::ICONE_ETAT_REFUSE,
//                "couleur" => self::COUlEUR_MUTED,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "choix_post_groupe_epreuve_filiere_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatChoixPostGroupeEpreuveFiliere::CODE_CATEGORIE,
//            ],
//        ];
//    }
//
//    protected function getEtatEtudiantSecondGroupeEpreuve() : array
//    {
//        $ordre = 1;
//        return [
//            //Préparation
//            [
//                "code" => "preparation_second_groupe_futur",
//                "libelle" => "À venir",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreparationSecondGroupe::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "preparation_second_groupe_en_cours",
//                "libelle" => "En cours",
//                "icone" => self::ICONE_ETAT_EN_COURS,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreparationSecondGroupe::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "preparation_second_groupe_termine",
//                "libelle" => "Terminées",
//                "icone" => self::ICONE_ETAT_TERMINE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatPreparationSecondGroupe::CODE_CATEGORIE,
//            ],
//            //Le second groupe d'épreuve
//            [
//                "code" => "second_groupe_epreuve_futur",
//                "libelle" => "À venir",
//                "icone" => self::ICONE_ETAT_FUTUR,
//                "couleur" => self::COUlEUR_INFO,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_en_cours",
//                "libelle" => "Épreuves en cours",
//                "icone" => self::ICONE_ETAT_EN_COURS,
//                "couleur" => self::COUlEUR_PRIMARY,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_termine",
//                "libelle" => "Épreuves terminées",
//                "icone" => self::ICONE_ETAT_TERMINE,
//                "couleur" => self::COUlEUR_SUCCESS,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_non_publie",
//                "libelle" => "Non publiées",
//                "icone" => self::ICONE_ETAT_WARNING,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_sans_epreuve",
//                "libelle" => "Inscrit a aucune épreuve du second groupe",
//                "icone" => self::ICONE_ETAT_WARNING,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_sans_epreuve_retard",
//                "libelle" => "Inscrit a aucune épreuve du second groupe",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_non_effectue",
//                "libelle" => "Non effectuées",
//                "icone" => self::ICONE_ETAT_NON_EFFECTUE,
//                "couleur" => self::COUlEUR_DANGER,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//            [
//                "code" => "second_groupe_epreuve_indetermine",
//                "libelle" => "État indeterminé",
//                "icone" => self::ICONE_ETAT_INDETERMINE,
//                "couleur" => self::COUlEUR_WARNING,
//                "ordre" => $ordre++,
//                "categorie_id" => EtatSecondGroupeEpreuve::CODE_CATEGORIE,
//            ],
//        ];
//    }


}