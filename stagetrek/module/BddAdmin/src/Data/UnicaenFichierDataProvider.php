<?php

namespace BddAdmin\Data;


use Application\Provider\Fichier\NatureFichierProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenFichierDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig = [
            'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
            'key'                     => 'code',
            'options'                 => ['update'  => true, 'soft-delete' => true, 'delete' => true,],
        ];
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_fichier_nature(): array
    {
        return [
            [
                "code" => NatureFichierProvider::CONVENTION,
                "libelle" => 'Convention',
                "description" => "Convention de stage",
                "ordre" => 1
            ],
        ];
    }
}