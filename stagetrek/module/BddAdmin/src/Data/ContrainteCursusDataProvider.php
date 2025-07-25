<?php

namespace BddAdmin\Data;


use Application\Entity\Db\AdresseType;
use Application\Entity\Db\ContrainteCursusPortee;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class ContrainteCursusDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'contrainte_cursus_portee' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options'                 => ['update'  => false, 'soft-delete' => false, 'delete' => false,],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function contrainte_cursus_portee(): array
    {
        return[
            [
                "code" => ContrainteCursusPortee::GENERALE,
                "libelle" => 'Générale',
                "ordre" => 1
            ],
            [
                "code" => ContrainteCursusPortee::CATEGORIE,
                "libelle" => 'Catégorie stage',
                "ordre" => 2
            ],
            [
                "code" => ContrainteCursusPortee::TERRAIN,
                "libelle" => 'Terrain de stage',
                "ordre" => 3
            ],
        ];
    }
}