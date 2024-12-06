<?php


namespace Application\Assertion;

use Application\Controller\Terrain\CategorieStageController as Controller;
use Application\Entity\Db\CategorieStage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\TerrainPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class CategorieStageAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $categorieStage = $this->getCategorieStage();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER
                => true,
            Controller::ACTION_AJOUTER,  Controller::ACTION_IMPORTER
                => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($categorieStage),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($categorieStage),
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
        $categorieStage = ($entity instanceof CategorieStage) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $categorieStage = $entity->get(CategorieStage::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::CATEGORIE_STAGE_AFFICHER => true,
            Privilege::CATEGORIE_STAGE_AJOUTER => $this->assertAjouter(),
            Privilege::CATEGORIE_STAGE_MODIFIER => $this->assertModifier($categorieStage),
            Privilege::CATEGORIE_STAGE_SUPPRIMER => $this->assertSupprimer($categorieStage),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?CategorieStage $categorieStage) : bool
    {
        return isset($categorieStage);
    }

    private function assertSupprimer(?CategorieStage $categorieStage) : bool
    {
        if(!isset($categorieStage)){return false;}
        if(!$categorieStage->getTerrainsStages()->isEmpty()){return false;}
        return true;
    }

    protected function getCategorieStage() : ?CategorieStage
    {
        $id = intval($this->getParam('categorieStage', 0));
        return $this->getObjectManager()->getRepository(CategorieStage::class)->find($id);
    }

}
