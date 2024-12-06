<?php


namespace Application\Assertion;

use Application\Controller\Terrain\TerrainStageController as Controller;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Preference;
use Application\Entity\Db\TerrainStage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\TerrainPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class TerrainStageAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        if (!$role instanceof RoleInterface) return false;

        $terrainStage = $this->getTerrainStage();
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER
                => true,
            Controller::ACTION_AFFICHER,
            Controller::ACTION_LISTER_CONTACTS,
            Controller::ACTION_AFFICHER_MODELE_CONVENTION,
                => $this->assertAfficher($terrainStage),
            Controller::ACTION_AJOUTER,  Controller::ACTION_IMPORTER
                => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($terrainStage),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($terrainStage),
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
        $terrainStage = ($entity instanceof TerrainStage) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $terrainStage = $entity->get(TerrainStage::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::TERRAIN_STAGE_AFFICHER => $this->assertAfficher($terrainStage),
            Privilege::TERRAIN_STAGE_AJOUTER => $this->assertAjouter(),
            Privilege::TERRAIN_STAGE_MODIFIER => $this->assertModifier($terrainStage),
            Privilege::TERRAIN_STAGE_SUPPRIMER => $this->assertSupprimer($terrainStage),
            default => false,
        };
    }

    private function assertAfficher(?TerrainStage $terrainStage) : bool
    {
        return isset($terrainStage);
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?TerrainStage $terrainStage) : bool
    {
        return isset($terrainStage);
    }


    private function assertSupprimer(?TerrainStage $terrainStage) : bool
    {
        if(!isset($terrainStage)){return false;}
        //On ne peux pas supprimer un terrain de stage s'il est associé à une affectation ou a des préférences
        $affectation = $this->getObjectManager()->getRepository(AffectationStage::class)->findOneBy(["terrainStage" => $terrainStage->getId()]);
        if (isset($affectation)) {return false;}
        $affectation = $this->getObjectManager()->getRepository(AffectationStage::class)->findOneBy(["terrainStageSecondaire" => $terrainStage->getId()]);
        if (isset($affectation)) {return false;}
        $preference = $this->getObjectManager()->getRepository(Preference::class)->findOneBy(["terrainStage" => $terrainStage->getId()]);
        if (isset($preference)) {return false;}
        $preference = $this->getObjectManager()->getRepository(Preference::class)->findOneBy(["terrainStageSecondaire" => $terrainStage->getId()]);
        if (isset($preference)) {return false;}

        return true;
    }

    protected function getTerrainStage() : ?TerrainStage
    {
        $id = intval($this->getParam('terrainStage', 0));
        return $this->getObjectManager()->getRepository(TerrainStage::class)->find($id);
    }

}
