<?php

namespace BddAdmin\Data;


use Application\Entity\Db\Contact;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class ContactDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'contact' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options'                 => ['update'  => false, 'soft-delete' => false, 'delete' => false,],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function contact(): array
    {
        return[
            [
                "code" => Contact::CODE_ASSISTANCE,
                "libelle" => "Assistance",
                "display_name" => "Assistance",
                "mail" => "aRemplacer@faux-mail.fr",
                "telephone" => "",
                "actif" => true,
            ],
        ];
    }
}