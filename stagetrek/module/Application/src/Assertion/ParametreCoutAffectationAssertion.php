<?php


namespace Application\Assertion;

use Application\Controller\Parametre\NiveauEtudeController as Controller;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ParametrePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ParametreCoutAffectationAssertion extends AbstractAssertion
{
    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $parametreCoutAffectation = $this->getParametreCoutAffectation();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($parametreCoutAffectation),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($parametreCoutAffectation),
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
        $parametreCoutAffectation = ($entity instanceof ParametreCoutAffectation) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $parametreCoutAffectation = $entity->get(ParametreCoutAffectation::RESOURCE_ID);
        }
        return match ($privilege) {
            ParametrePrivileges::PARAMETRE_AFFICHER => true,
            ParametrePrivileges::PARAMETRE_AJOUTER => $this->assertAjouter(),
            ParametrePrivileges::PARAMETRE_MODIFIER => $this->assertModifier($parametreCoutAffectation),
            ParametrePrivileges::PARAMETRE_SUPPRIMER => $this->assertSupprimer($parametreCoutAffectation),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?ParametreCoutAffectation $parametreCoutAffectation) : bool
    {
        return isset($parametreCoutAffectation);
    }


    protected function assertSupprimer(?ParametreCoutAffectation $parametreCoutAffectation): bool
    {
        return isset($parametreCoutAffectation);
    }

    protected function getParametreCoutAffectation() : ?ParametreCoutAffectation
    {
        $id = intval($this->getParam('parametreCoutAffectation', 0));
        return $this->getObjectManager()->getRepository(ParametreCoutAffectation::class)->find($id);
    }

}