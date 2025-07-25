<?php

namespace BddAdmin\Data;


use Application\Entity\Db\Source;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class SourceDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig = [
            'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
            'key'     => 'code',
            'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
        ];
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function source(): array
    {
        return [
            [
                'code' => Source::STAGETREK,
                'libelle' => "Stagetrek",
                'importable' => false,
                'synchro_insert_enabled' => false,
                'synchro_update_enabled' => false,
                'synchro_undelete_enabled' => false,
                'synchro_delete_enabled' => false,
            ],
            [//TODO : source par défaut qui devrait disparaitre
                'code' => Source::LDAP,
                'libelle' => "Ldap",
                'importable' => true,
                'synchro_insert_enabled' => true,
                'synchro_update_enabled' => true,
                'synchro_undelete_enabled' => true,
                'synchro_delete_enabled' => false,
            ],
        ];
    }
}