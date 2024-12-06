<?php


namespace Application\Assertion;

use Application\Controller\Affectation\AffectationController as Controller;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\SessionStage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\StagePrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class AffectationStageAssertion extends AbstractAssertion
{
    /**
     * @param string $controller
     * @param string|null  $action
     * @param string|null $privilege
     * @return boolean
     */
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        $session = $this->getSessionStage();
        $affectation = $this->getAffectationStage();

        return match ($action) {
            Controller::ACTION_LISTER => $this->assertLister(),
            Controller::ACTION_AFFICHER => $this->assertAfficher($affectation),
            Controller::ACTION_MODIFIER => $this->assertModifier($affectation),
            Controller::ACTION_MODIFIER_AFFECTATIONS => $this->assertModifierAffectations($session),
            Controller::ACTION_CALCULER_AFFECTATIONS => $this->assertRunProcedure($session),
            Controller::ACTION_EXPORTER => $this->assertExporter($session),
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
        $affectation = ($entity instanceof AffectationStage) ? $entity : null;
        $session = ($entity instanceof SessionStage) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $affectation = $entity->get(AffectationStage::RESOURCE_ID);
            $session = $entity->get(SessionStage::RESOURCE_ID);
        }
        return match (true) {
            $privilege == Privilege::AFFECTATION_AFFICHER && isset($affectation) => $this->assertAfficher($affectation),
            $privilege == Privilege::AFFECTATION_AFFICHER && isset($session) => $this->assertExporter($session),
            $privilege == Privilege::AFFECTATION_MODIFIER && isset($affectation) => $this->assertModifier($affectation),
            $privilege == Privilege::AFFECTATION_MODIFIER && isset($session) => $this->assertModifierAffectations($session),
            $privilege == Privilege::AFFECTATION_RUN_PROCEDURE => $this->assertRunProcedure($session),
            $privilege == Privilege::AFFECTATION_PRE_VALIDER && isset($affectation) => $this->assertPreValider($affectation),
            $privilege == Privilege::AFFECTATION_PRE_VALIDER && isset($session) => $this->assertPreValiderAffectations($session),
            $privilege == Privilege::COMMISSION_VALIDER_AFFECTATIONS && isset($affectation) => $this->assertValider($affectation),
            $privilege == Privilege::COMMISSION_VALIDER_AFFECTATIONS && isset($session) => $this->assertValiderAffectations($session),
            default => false,
        };
    }

    private function assertLister() : bool
    {
        return true;
    }

//    TODO : a revoir pour ne pas autoriser d'afficher la session de stage de l'étudiant
    private function assertAfficher(?AffectationStage $affectationStage) : bool
    {
        return isset($affectationStage);
    }

    private function assertModifier(?AffectationStage $affectationStage) : bool
    {
        return isset($affectationStage);
    }
    private function assertModifierAffectations(?SessionStage $sessionStage) : bool
    {
        return isset($sessionStage);
    }

    private function assertRunProcedure(?SessionStage $sessionStage) : bool
    {
        if(!isset($sessionStage)){return false;}
//        if(new DateTime() >= $sessionStage->getDateDebutStage()){return false;}
        return true;
    }

    private function assertPreValider(?AffectationStage $affectationStage) : bool
    {
        return isset($affectationStage);
    }
    private function assertPreValiderAffectations(?SessionStage $sessionStage) : bool
    {
        return isset($sessionStage);
    }

    private function assertValider(?AffectationStage $affectationStage) : bool
    {
        return isset($affectationStage);
    }
    private function assertValiderAffectations(?SessionStage $sessionStage) : bool
    {
        return isset($sessionStage);
    }

    private function assertExporter(?SessionStage $sessionStage) : bool
    {
        return isset($sessionStage);
    }

    protected function getSessionStage() : ?SessionStage
    {
        $id = intval($this->getParam('sessionStage', 0));
        return $this->getObjectManager()->getRepository(SessionStage::class)->find($id);
    }

    protected function getAffectationStage() : ?AffectationStage
    {
        $id = intval($this->getParam('affectationStage', 0));
        return $this->getObjectManager()->getRepository(AffectationStage::class)->find($id);
    }

}