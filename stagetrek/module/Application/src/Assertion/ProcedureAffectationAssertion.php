<?php


namespace Application\Assertion;

use Application\Controller\Affectation\ProcedureAffectationController as Controller;
use Application\Entity\Db\ProcedureAffectation;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\StagePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ProcedureAffectationAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $procedure = $this->getProcedureAffectation();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AFFICHER => $this->assertAfficher($procedure),
            Controller::ACTION_MODIFIER => $this->assertModifier($procedure),
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
        $procedure = ($entity instanceof ProcedureAffectation) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $procedure = $entity->get(ProcedureAffectation::RESOURCE_ID);
        }
        return match ($privilege) {
            StagePrivileges::PROCEDURE_AFFICHER =>  $this->assertAfficher($procedure),
            StagePrivileges::PROCEDURE_MODIFIER => $this->assertModifier($procedure),
            default => false,
        };
    }

    private function assertAfficher(?ProcedureAffectation $procedureAffectation) : bool
    {
        return isset($procedureAffectation);
    }

    private function assertModifier(?ProcedureAffectation $procedureAffectation) : bool
    {
        return isset($procedureAffectation);
    }

    protected function getProcedureAffectation() : ?ProcedureAffectation
    {
        $id = intval($this->getParam('procedureAffectation', 0));
        return $this->getObjectManager()->getRepository(ProcedureAffectation::class)->find($id);
    }

}