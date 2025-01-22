<?php

namespace Application\Provider\Roles;

use Application\Entity\Db\Etudiant;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use UnicaenUtilisateur\Entity\Db\RoleInterface;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Provider\Identity\AbstractIdentityProvider;

class IdentityProvider extends AbstractIdentityProvider
{

    use EtudiantServiceAwareTrait;

    /**
     * @param string $code
     * @return User[]|null
     */
    public function computeUsersAutomatiques(string $code): ?array
    {
        switch ($code) {
            case  RolesProvider::ROLE_ETUDIANT :
                $etudiants = $this->getEtudiantService()->findAll();
                $etudiants = array_filter($etudiants, function (Etudiant $e){
                    return ($e->getUser() != null);});
                $users=[];
                foreach ($etudiants as $e){
                    $users[] = $e->getUser();
                }
                return $users;
        }
        return null;
    }

    /**
     * @param User|null $user
     * @return string[]|RoleInterface[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function computeRolesAutomatiques(?User $user = null): array
    {
        $roles = [];
        if ($user === null) {
            $user = $this->getUserService()->getConnectedUser();
        }
        $roleEtudiant = $this->getRoleService()->findByRoleId(RolesProvider::ROLE_ETUDIANT);
        $roleTech = $this->getRoleService()->findByRoleId(RolesProvider::ROLE_ADMINISTATEUR);

        if ($user !== null) {
            $isEtudiant = $this->getEtudiantService()->findOneBy(['user' => $user]);
            if ($isEtudiant) {
                $roles[] = $roleEtudiant;
            }
//            association entre l'étudiant et le compte utilisateur en se basant sur l'adresse mail si possible
            if(!isset($isEtudiant)){
                /** @var Etudiant $etudiant */
                $etudiant = $this->getEtudiantService()->findOneBy(['email' => $user->getEmail()]);
                if($etudiant){
                    $roles[] = $roleEtudiant;
                    $etudiant->setUser($user);
                    $this->getEtudiantService()->update($etudiant);
                }
            }

            if($roleTech->getUsers()->isEmpty()){
//                Si aucun utilisateur n'as le role Administrateur Technique
//              et que l'utilisateur connecté fait partie des utilisateur par défaut, on lui attribue le role
                foreach ($this->defaultUsers as $default) {
                    if ($default == $user->getUsername()) {
                        $user->addRole($roleTech);
                        $user->setLastRole($roleTech);
                        $this->getUserService()->update($user);
                        break;
                    }
                }
            }
        }
        return $roles;
    }

    protected array $defaultUsers = [];

    public function getDefaultUsers(): array
    {
        return $this->defaultUsers;
    }

    public function setDefaultUsers(array $defaultUsers): void
    {
        $this->defaultUsers = $defaultUsers;
    }


//    /**
//     * @return string[]|RoleInterface[]
//     */
//    public function getIdentityRoles()
//    {
//        $this->roles = $this->roles = $this->computeRolesAutomatiques();
//        return $this->roles;
//    }
//
//    /**
//     * @param ChainEvent $event
//     */
//    public function injectIdentityRoles(ChainEvent $event)
//    {
//        $event->addRoles($this->getIdentityRoles());
//    }
}