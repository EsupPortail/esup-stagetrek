<?php


namespace Application\Assertion;

use Application\Controller\Convention\ConventionStageController as Controller;
use Application\Entity\Db\Stage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ConventionsPrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ConventionStageAssertion extends AbstractAssertion
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
        if (!$role instanceof RoleInterface) return false;
        $stage = $this->getStage();
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->assertAfficher($stage),
            Controller::ACTION_GENERER => $this->assertGenerer($stage),
            Controller::ACTION_TELEVERSER => $this->asserTeleverser($stage),
            Controller::ACTION_TELECHARGER => $this->assertTelecharger($stage),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($stage),
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
        $stage = ($entity instanceof Stage) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $stage = $entity->get(Stage::RESOURCE_ID);
        }
        return match ($privilege) {
            ConventionsPrivileges::CONVENTION_AFFICHER => $this->assertAfficher($stage),
            ConventionsPrivileges::CONVENTION_TELEVERSER => $this->asserTeleverser($stage),
            ConventionsPrivileges::CONVENTION_GENERER => $this->assertGenerer($stage),
            ConventionsPrivileges::CONVENTION_MODIFIER => $this->assertModifier($stage),
            ConventionsPrivileges::CONVENTION_TELECHARGER => $this->assertTelecharger($stage),
            ConventionsPrivileges::CONVENTION_SUPPRIMER => $this->assertSupprimer($stage),
            default => false,
        };
    }

    private function assertAfficher(?Stage $stage) : bool
    {
        return isset($stage);
    }
    private function asserTeleverser(?Stage $stage): bool
    {
        return isset($stage);
    }

    private function assertGenerer(?Stage $stage): bool
    {
        if (!isset($stage)){return false;}
        if($stage->isNonEffectue()){return false;}
//        La convention existe déjà
        if($stage->hasConventionStage()){return false;}
//        Il faut nécessairement que le terrains de stage soit conventionné
        $affectation = $stage->getAffectationStage();
        if(!isset($affectation)){return false;}
        $terrain = $stage->getTerrainStage();
        if(!isset($terrain)){return false;}
        $modele = $terrain->getModeleConventionStage();
        if(!isset($modele)){return false;}

        return true;
    }

    private function assertModifier(?Stage $stage): bool
    {
        if (!isset($stage)){return false;}
        if(!$stage->hasConventionStage()){return false;}
        $convention = $stage->getConventionStage();
        if(!$convention->hasRendu()){return false;}
        return true;
    }

    private function assertTelecharger(?Stage $stage): bool
    {
        if(!isset($stage)){return false;}
        if(!$stage->hasConventionStage()){return false;}
        $conventionStage = $stage->getConventionStage();
        if(!$conventionStage->hasFile()){return false;}
        return true;
    }

    private function assertSupprimer(?Stage $stage): bool
    {
        if(!isset($stage)){return false;}
        if(!$stage->hasConventionStage()){return false;}
        return true;
    }

    protected function getStage() : ?Stage
    {
        $id = intval($this->getParam('stage', 0));
        return $this->getObjectManager()->getRepository(Stage::class)->find($id);
    }


}
