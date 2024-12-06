<?php


namespace Application\Assertion;

use Application\Controller\Convention\ModeleConventionController as Controller;
use Application\Entity\Db\ModeleConventionStage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ConventionsPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class ModeleConventionStageAssertion extends AbstractAssertion
{
    use UserServiceAwareTrait;

   protected function assertController($controller, $action = null, $privilege = null): bool
   {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        $modeleConventionStage = $this->getModeleConventionStage();
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AFFICHER => $this->assertAfficher($modeleConventionStage),
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($modeleConventionStage),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($modeleConventionStage),
            default => false,
        };
    }

    protected function assertEntity(ResourceInterface $entity, $privilege = null): bool
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        // si le rôle ne possède pas le privilège
        if (!parent::assertEntity($entity, $privilege)) {
            return false;
        }

        $modele = ($entity instanceof ModeleConventionStage) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $modele = $entity->get(ModeleConventionStage::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::MODELE_CONVENTION_AFFICHER => $this->assertAfficher($modele),
            Privilege::MODELE_CONVENTION_AJOUTER => $this->assertAjouter(),
            Privilege::MODELE_CONVENTION_MODIFIER => $this->assertModifier($modele),
            Privilege::MODELE_CONVENTION_SUPPRIMER => $this->assertSupprimer($modele),
            default => false,
        };
    }


    private function assertAfficher(?ModeleConventionStage $modeleConventionStage) : bool
    {
        return isset($modeleConventionStage);
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?ModeleConventionStage $modeleConventionStage) : bool
    {
        return isset($modeleConventionStage);
    }

    private function assertSupprimer(?ModeleConventionStage $modeleConventionStage) : bool
    {
        if(!isset($modeleConventionStage)){return false;}
        if(!$modeleConventionStage->getTerrainsStages()->isEmpty()){return false;}
        return true;
    }


    private function getModeleConventionStage()
    {
        $id = intval($this->getParam('modeleConventionStage'));
        return $this->getObjectManager()->getRepository(ModeleConventionStage::class)->find($id);
    }

}
