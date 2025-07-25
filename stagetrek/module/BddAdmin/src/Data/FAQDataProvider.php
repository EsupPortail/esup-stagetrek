<?php

namespace BddAdmin\Data;


use Application\Entity\Db\Source;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class FAQDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'faq_categorie_question' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
                break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function faq_categorie_question(): array
    {
        return [
            [
                "code" => 'generale',
                "libelle" => 'Générale',
                "ordre" => 1
            ],
        ];
    }
}