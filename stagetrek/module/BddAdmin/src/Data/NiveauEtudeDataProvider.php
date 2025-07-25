<?php

namespace BddAdmin\Data;


use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class NiveauEtudeDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'niveau_etude' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'id',
                    'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
                break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

//TODO : mettre un code au niveau d'étude et se servir du code pour le liens avec le parent
    public function niveau_etude(): array
    {
        $ordre = 1;
        return [
            [
                "id" => 1,
                "libelle" => '4ème année',
                "nb_stages" => 4,
                "niveau_etude_parent" => null,
                "active" => true,
                "ordre" => $ordre++,
            ], [
                "id" => 2,
                "libelle" => '5ème année',
                "nb_stages" => 3,
                "niveau_etude_parent" => 1,
                "active" => true,
                "ordre" => $ordre++,
            ], [
                "id" => 3,
                "libelle" => '6ème année',
                "nb_stages" => 5,
                "niveau_etude_parent" => 2,
                "active" => true,
                "ordre" => $ordre++,
            ],
        ];
    }
}