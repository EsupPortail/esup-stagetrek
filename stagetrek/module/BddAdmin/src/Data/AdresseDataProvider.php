<?php

namespace BddAdmin\Data;


use Application\Entity\Db\AdresseType;
use Application\Entity\Db\Contact;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class AdresseDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'adresse_type' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function adresse_type(): array
    {
        return[
            [
                "code" => AdresseType::TYPE_INCONNU,
                "libelle" => "Indeterminé",
            ],
            [
                "code" => AdresseType::TYPE_ETUDIANT,
                "libelle" => "Etudiant",
            ],
            [
                "code" => AdresseType::TYPE_TERRAIN_STAGE,
                "libelle" => "Terrain de stage",
            ],
        ];
    }
}