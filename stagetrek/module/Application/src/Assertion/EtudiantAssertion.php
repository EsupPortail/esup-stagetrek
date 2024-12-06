<?php


namespace Application\Assertion;

use Application\Controller\Etudiant\EtudiantController as Controller;
use Application\Entity\Db\Etudiant;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use UnicaenUtilisateur\Entity\Db\User;

class EtudiantAssertion extends AbstractAssertion
{
    /**
     * @param string $controller
     * @param string|null $action
     * @param string|null $privilege
     * @return boolean
     */
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        $etudiant = $this->getEtudiant();
        $user = $this->getUser();
        return match ($action) {
            Controller::ACTION_INDEX => true,
            Controller::ACTION_AFFICHER, Controller::ACTION_AFFICHER_INFOS, Controller::ACTION_LISTER_STAGES => $this->assertAfficher($etudiant),
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($etudiant),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($etudiant),
            Controller::ACTION_IMPORTER => $this->assertImporter(),
            Controller::ACTION_MON_PROFIL => $this->assertMonProfil($user),
            default => false,
        };
    }

    /**
     * @param \Laminas\Permissions\Acl\Resource\ResourceInterface $entity
     * @param null $privilege
     * @return bool
     */
    protected function assertEntity(ResourceInterface $entity, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }
        $etudiant = ($entity instanceof Etudiant) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $etudiant = $entity->get(Etudiant::RESOURCE_ID);
        }

        return match ($privilege) {
            Privilege::ETUDIANT_AFFICHER => $this->assertAfficher($etudiant),
            Privilege::ETUDIANT_AJOUTER => $this->assertAjouter(),
            Privilege::ETUDIANT_MODIFIER => $this->assertModifier($etudiant),
            Privilege::ETUDIANT_SUPPRIMER => $this->assertSupprimer($etudiant),
            default => false,
        };
    }

    private function assertAfficher(?Etudiant $etudiant) : bool
    {
        return isset($etudiant);
    }

    private function assertAjouter() : bool
    {
        return true;
    }
    private function assertModifier(?Etudiant $etudiant) : bool
    {
        return isset($etudiant);
    }

    private function assertSupprimer(?Etudiant $etudiant) : bool
    {
        if(!isset($etudiant)){return false;}
        $sessions = $etudiant->getSessionsStages();
        if(!$sessions->isEmpty()){
            return false;
        }
        return true;
    }

    private function assertImporter() : bool
    {
        return true;
    }

    /**
     * @param \UnicaenUtilisateur\Entity\Db\User|null $user
     * @return bool
     */
    private function assertMonProfil(?User $user): bool
    {
        if (!isset($user)) {return false; }
        /** @var Etudiant $etudiant */
        $etudiant = $this->getObjectManager()->getRepository(Etudiant::class)->findOneBy(['user' => $user->getId()]);
        return isset($etudiant);
    }

    protected function getEtudiant() : ?Etudiant
    {
        $id = intval($this->getParam('etudiant', 0));
        return $this->getObjectManager()->getRepository(Etudiant::class)->find($id);
    }

}