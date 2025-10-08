<?php

namespace BddAdmin\Data;


use Application\Provider\Roles\RolesProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenRoleDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig = [
            'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
            'key'                     => 'role_id',
            'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
        ];
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_utilisateur_role(): array
    {
        return [
            [
                'role_id' => RolesProvider::ADMIN_TECH,
                'libelle' => "Administrateur·trice Technique",
                'description' => "Administrateur·trice Technique",
                'is_default' => false,
                'is_auto' => false,
                'accessible_exterieur' => true,
                'displayed' => true,
            ],
            [
                'role_id' => RolesProvider::ADMIN_FONC,
                'libelle' => "Administrateur·trice Fonctionnel·le",
                'description' => "Administrateur·trice Fonctionnel·le",
                'is_default' => false,
                'is_auto' => false,
                'accessible_exterieur' => true,
                'displayed' => true,
            ],
            [
                'role_id' => RolesProvider::ETUDIANT,
                'libelle' => "Etudiant·e",
                'description' => "Etudiant·e",
                'is_default' => false,
                'is_auto' => true,
                'accessible_exterieur' => true,
                'displayed' => true,
            ],
            [
                'role_id' => RolesProvider::SCOLARITE,
                'libelle' => "Scolarité",
                'description' => "Gestionaire de scolarité",
                'is_default' => false,
                'is_auto' => false,
                'accessible_exterieur' => true,
                'displayed' => true,
            ],
            [
                'role_id' => RolesProvider::GARDE,
                'libelle' => "Stage et Garde",
                'description' => "Membres de la commission Stage et Garde",
                'is_default' => false,
                'is_auto' => false,
                'accessible_exterieur' => true,
                'displayed' => true,
            ],
            [
                'role_id' => RolesProvider::OBSERVATEUR,
                'libelle' => "Observateur·trice",
                'description' => "Observateur·trice",
                'is_default' => false,
                'is_auto' => false,
                'accessible_exterieur' => true,
                'displayed' => true,
            ],
        ];
    }
}