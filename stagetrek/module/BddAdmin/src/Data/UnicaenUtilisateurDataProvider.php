<?php

namespace BddAdmin\Data;


use Application\Provider\Roles\RolesProvider;
use Application\Provider\Roles\UserProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class UnicaenUtilisateurDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_utilisateur_user' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'id',
                    'options'                 => ['update'  => true, 'soft-delete' => false, 'delete' => false,],
                ];
            break;
            case 'unicaen_utilisateur_role_linker' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'                     => ['user_id', 'role_id'],
                    'options'                 => ['update'  => false, 'soft-delete' => false, 'delete' => false,
                        'columns' => [
                            'user_id'      => ['transformer' => 'select id from unicaen_utilisateur_user where username = %s'],
                            'role_id'      => ['transformer' => 'select id from unicaen_utilisateur_role where role_id = %s'],
                        ],
                    ],
                ];
            break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_utilisateur_user(): array
    {
        return [
            [
                'id' => UserProvider::APP_USER_ID,
                'username' => UserProvider::APP_USER,
                'display_name' => "Parcours Accès Santé",
                'email' => "",
                'password' => "app",
                'accessible_exterieur' => false,
                'displayed' => true,
            ],
        ];
    }

    use UserServiceAwareTrait;
/*  Config à la clé  ['unicaen-bddadmin']['data']['unicaen-utilisateur']
 Doit être sous la forme pour leurs attribuée les roles désiré
    'roleId1' => ['username1', 'username2']
    'roleId2' => ['username1', 'username3']
*/
    protected array $defaultUsersRoles = [];
    public function getDefaultUser(): array
    {
        return $this->defaultUsersRoles;
    }
    public function setDefaultUser(array $defaultUser): void
    {
        $this->defaultUsersRoles = $defaultUser;
    }

    public function unicaen_utilisateur_role_linker() : array
    {
        //Permet d'être sur que le tableau n'est pas vide : on donne le role observateur à APP
        $data= [['user_id' => UserProvider::APP_USER, 'role_id' => RolesProvider::OBSERVATEUR]];
        foreach ($this->defaultUsersRoles as $role => $users) {
            foreach ($users as $userName) {
                $u = $this->getUserService()->findByUsername($userName);
                if(isset($u)){
                    $data[] = ['user_id' => $userName, 'role_id' => $role];
                }
            }
        }
        return $data;
    }
}