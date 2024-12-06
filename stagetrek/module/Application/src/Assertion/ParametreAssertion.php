<?php


namespace Application\Assertion;

use Application\Controller\Parametre\NiveauEtudeController as Controller;
use Application\Entity\Db\Parametre;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ParametrePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ParametreAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $parametre = $this->getParametre();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($parametre),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($parametre),
            default => false,
        };
    }

    protected function assertEntity(ResourceInterface $entity, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }
        $parametre = ($entity instanceof Parametre) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $parametre = $entity->get(Parametre::RESOURCE_ID);
        }
        return match ($privilege) {
            ParametrePrivileges::PARAMETRE_AFFICHER => true,
            ParametrePrivileges::PARAMETRE_AJOUTER => $this->assertAjouter(),
            ParametrePrivileges::PARAMETRE_MODIFIER => $this->assertModifier($parametre),
            ParametrePrivileges::PARAMETRE_SUPPRIMER => $this->assertSupprimer($parametre),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?Parametre $parametre) : bool
    {
        return isset($parametre);
    }


    protected function assertSupprimer(?Parametre $parametre): bool
    {
        // LAs suppression n'est pas possible pour des raisons de sécurité
//        if(!isset($parametre)){return false;}
        return false;
    }

    protected function getParametre() : ?Parametre
    {
        $id = intval($this->getParam('parametre', 0));
        return $this->getObjectManager()->getRepository(Parametre::class)->find($id);
    }

}