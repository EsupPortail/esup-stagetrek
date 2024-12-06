<?php


namespace Application\Assertion;

use Application\Controller\Contrainte\ContrainteCursusController as Controller;
use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ParametrePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ContrainteCursusAssertion extends AbstractAssertion
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

        /** @var ContrainteCursus $contrainte */
        $contrainte = $this->getContrainteCursus();
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($contrainte),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($contrainte),
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
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte =  ($entity instanceof ContrainteCursusEtudiant) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $contrainte = $entity->get(ContrainteCursusEtudiant::RESOURCE_ID);
        }
        return match ($privilege) {
            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AFFICHER => $this->assertAfficher($contrainte),
            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_AJOUTER => $this->assertAjouter(),
            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_MODIFIER => $this->assertModifier($contrainte),
            ParametrePrivileges::PARAMETRE_CONTRAINTE_CURSUS_SUPPRIMER => $this->assertSupprimer($contrainte),

                  default => false,
        };
    }

    protected function getContrainteCursus() : ?ContrainteCursus
    {
        $id = intval($this->getParam('contrainteCursus', 0));
        return $this->getObjectManager()->getRepository(ContrainteCursus::class)->find($id);
    }

    private function assertAfficher(?ContrainteCursus $contrainte) : bool
    {
        return isset($contrainte);
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?ContrainteCursus $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        return true;
    }

    private function assertSupprimer(?ContrainteCursus $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        return true;
    }

}
