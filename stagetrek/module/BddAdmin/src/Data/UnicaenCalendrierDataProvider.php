<?php

namespace BddAdmin\Data;


use Application\Entity\Db\SessionStage;
use Application\Misc\Util;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenCalendrierDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_calendrier_calendrier_type' :
            case 'unicaen_calendrier_date_type' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
                ];
            break;
            case 'unicaen_calendrier_calendriertype_datetype' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'                     => ['calendrier_type_id', 'date_type_id'],
                    'options'                 => ['update'  => false, 'soft-delete' => true, 'delete' => true,
                        'columns' => [
                            'calendrier_type_id'      => ['transformer' => 'select id from unicaen_calendrier_calendrier_type where code = %s'],
                            'date_type_id' => ['transformer' => 'select id from unicaen_calendrier_date_type where code = %s'],
                        ],
                    ],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_calendrier_calendrier_type(): array
    {
        return [['code' => SessionStage::CALENDRIER_TYPE, 'libelle' => "Session de stage"]];
    }

    public function unicaen_calendrier_date_type() : array
    {
        return [
            [
               'code' => SessionStage::DATE_CALCUL_ORDRES_AFFECTATIONS,
                'libelle' => "Calcul des ordres d'affectations",
                'description' => "Date de calcul automatiques des ordres d'affectations",
                'interval' => false,
                'duree_a_priori' => null
            ],
            [
               'code' => SessionStage::DATES_CHOIX,
                'libelle' => "Procédure de choix",
                'description' => "Période durant laquelle les étudiant".Util::POINT_MEDIANT."s doivent définir leurs préférences sur les terrains de stages",
                'interval' => true,
                'duree_a_priori' => 15
            ],
            [
               'code' => SessionStage::DATES_COMMISSION,
                'libelle' => "Commission d'affectation des stages",
                'description' => "Dates de la commission Stages et gardes",
                'interval' => true,
                'duree_a_priori' => 1
            ],
            [
               'code' => SessionStage::DATES_SESSIONS,
                'libelle' => "Dates de la session de stages",
                'description' => "Dates de la session de stages",
                'interval' => true,
                'duree_a_priori' => 30
            ],
            [
               'code' => SessionStage::DATES_PERIODE_STAGES,
                'libelle' => "Dates d'une période de stage",
                'description' => "Dates effective d'une période de stage",
                'interval' => true,
                'duree_a_priori' => 30
            ],
            [
                'code' => SessionStage::DATES_VALIDATIONS,
                'libelle' => "Phase de validation du stage",
                'description' => "Période durant laquelle les responsables de stages doivent évaluer le stage",
                'interval' => true,
                'duree_a_priori' => 15
            ],
            [
                'code' => SessionStage::DATES_EVALUATIONS,
                'libelle' => "Phase d'évaluation du stage",
                'description' => "Période durant laquelle les étudiant".Util::POINT_MEDIANT."s peuvent évaluer le stage",
                'interval' => true,
                'duree_a_priori' => 15
            ],
        ];
    }


    const CALENDRIER_TYPE = "session_stage";
    const DATE_CALCUL_ORDRES_AFFECTATIONS = "dates_calcul_ordres_affectations";
    const DATES_CHOIX = "dates_choix";
    const DATES_STAGES = "dates_stages";
    const DATES_COMMISSION = "dates_commission";
    const DATES_VALIDATIONS = "dates_validations";
    const DATES_EVALUATIONS = "dates_evaluations";

    public function unicaen_calendrier_calendriertype_datetype() : array
    {
        $datesTypes = [
            SessionStage::DATE_CALCUL_ORDRES_AFFECTATIONS,
            SessionStage::DATES_CHOIX,
            SessionStage::DATES_COMMISSION,
            SessionStage::DATES_PERIODE_STAGES,
            SessionStage::DATES_VALIDATIONS,
            SessionStage::DATES_EVALUATIONS,
        ];
        $res = [];
        foreach ($datesTypes as $dateType) {
            $res[] =[
                'calendrier_type_id' => SessionStage::CALENDRIER_TYPE,
                'date_type_id' => $dateType,
            ];
        }
        return $res;
    }
}