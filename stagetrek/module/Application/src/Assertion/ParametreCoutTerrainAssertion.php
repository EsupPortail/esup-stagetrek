<?php


namespace Application\Assertion;

use Application\Controller\Parametre\NiveauEtudeController as Controller;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ParametrePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ParametreCoutTerrainAssertion extends AbstractAssertion
{
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $parametreCoutTerrain = $this->getParametreCoutTerrain();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($parametreCoutTerrain),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($parametreCoutTerrain),
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
        $parametreCoutTerrain = ($entity instanceof ParametreTerrainCoutAffectationFixe) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $parametreCoutTerrain = $entity->get(ParametreTerrainCoutAffectationFixe::RESOURCE_ID);
        }
        return match ($privilege) {
            ParametrePrivileges::PARAMETRE_AFFICHER => true,
            ParametrePrivileges::PARAMETRE_AJOUTER => $this->assertAjouter(),
            ParametrePrivileges::PARAMETRE_MODIFIER => $this->assertModifier($parametreCoutTerrain),
            ParametrePrivileges::PARAMETRE_SUPPRIMER => $this->assertSupprimer($parametreCoutTerrain),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?ParametreTerrainCoutAffectationFixe $parametreCoutTerrain) : bool
    {
        return isset($parametreCoutTerrain);
    }


    protected function assertSupprimer(?ParametreTerrainCoutAffectationFixe $parametreCoutTerrain): bool
    {
        return isset($parametreCoutTerrain);
    }

    protected function getParametreCoutTerrain() : ?ParametreTerrainCoutAffectationFixe
    {
        $id = intval($this->getParam('parametreTerrainCoutAffectationFixe', 0));
        return $this->getObjectManager()->getRepository(ParametreTerrainCoutAffectationFixe::class)->find($id);
    }

}