<?php

namespace BddAdmin\Data;


use Application\Entity\Db\Source;
use Application\Entity\Db\TerrainStageNiveauDemande;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class TerrainDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'terrain_stage_niveau_demande' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
                break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function terrain_stage_niveau_demande(): array
    {
        $ordre = 1;
        return [
            [
                "code" => TerrainStageNiveauDemande::RANG_1,
                "libelle" => '1er décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_2,
                "libelle" => '2nd décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_3,
                "libelle" => '3ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_4,
                "libelle" => '4ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_5,
                "libelle" => '5ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_6,
                "libelle" => '6ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_7,
                "libelle" => '7ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_8,
                "libelle" => '8ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_9,
                "libelle" => '9ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::RANG_10,
                "libelle" => '10ème décile',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::NO_DEMANDE,
                "libelle" => 'Aucune demande',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::FERME,
                "libelle" => 'Terrain fermé',
                "ordre" => $ordre++,
            ],
            [
                "code" => TerrainStageNiveauDemande::INDETERMINE,
                "libelle" => 'Indéterminé',
                "ordre" => $ordre++,
            ],
        ];
    }
}