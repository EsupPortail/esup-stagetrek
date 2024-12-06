<?php


namespace Application\Assertion;

use Application\Controller\Contrainte\ContrainteCursusEtudiantController;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Etudiant;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ContrainteCursusEtudiantAssertion extends AbstractAssertion
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

        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $this->getContrainteCursusEtudiant();
        $etudiant = $this->getEtudiant();
        return match ($action) {
            ContrainteCursusEtudiantController::ACTION_LISTER => $this->assertLister($etudiant),
            ContrainteCursusEtudiantController::ACTION_AFFICHER => $this->assertAfficher($contrainte),
            ContrainteCursusEtudiantController::ACTION_MODIFIER => $this->assertModifier($contrainte),
            ContrainteCursusEtudiantController::ACTION_ACTIVER => $this->assertActiver($contrainte),
            ContrainteCursusEtudiantController::ACTION_DESACTIVER => $this->assertDesactiver($contrainte),
            ContrainteCursusEtudiantController::ACTION_VALIDER => $this->assertValider($contrainte),
            ContrainteCursusEtudiantController::ACTION_INVALIDER => $this->assertInvalider($contrainte),
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
            $etudiant = $entity->get(Etudiant::RESOURCE_ID);
        }

        if($privilege == EtudiantPrivileges::ETUDIANT_CONTRAINTES_AFFICHER && isset($etudiant)){ return $this->assertLister($etudiant);}
        return match ($privilege) {
            EtudiantPrivileges::ETUDIANT_CONTRAINTES_AFFICHER => $this->assertAfficher($contrainte),
            EtudiantPrivileges::ETUDIANT_CONTRAINTE_MODIFIER => $this->assertModifier($contrainte),
            EtudiantPrivileges::ETUDIANT_CONTRAINTE_ACTIVER => $this->assertActiver($contrainte),
            EtudiantPrivileges::ETUDIANT_CONTRAINTE_DESACTIVER => $this->assertDesactiver($contrainte),
            EtudiantPrivileges::ETUDIANT_CONTRAINTE_VALIDER => $this->assertValider($contrainte),
            EtudiantPrivileges::ETUDIANT_CONTRAINTE_INVALIDER => $this->assertInvalider($contrainte),
                  default => false,
        };
    }

    protected function getContrainteCursusEtudiant() : ?ContrainteCursusEtudiant
    {
        $id = intval($this->getParam('contrainteCursusEtudiant', 0));
        return $this->getObjectManager()->getRepository(ContrainteCursusEtudiant::class)->find($id);
    }

    protected function getEtudiant() : ?Etudiant
    {
        $id = intval($this->getParam('etudiant', 0));
        return $this->getObjectManager()->getRepository(Etudiant::class)->find($id);
    }

    private function assertLister(?Etudiant $etudiant) : bool
    {
        return isset($etudiant);
    }
    private function assertAfficher(?ContrainteCursusEtudiant $contrainte) : bool
    {
        return isset($contrainte);
    }

    private function assertModifier(?ContrainteCursusEtudiant $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        if(!$contrainte->isActive()){return false;}
        return true;
    }

    private function assertActiver(?ContrainteCursusEtudiant $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        if($contrainte->isActive()){return false;}
        return true;
    }

    private function assertDesactiver(?ContrainteCursusEtudiant $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        if(!$contrainte->isActive()){return false;}
        return true;
    }

    private function assertValider(?ContrainteCursusEtudiant $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        if(!$contrainte->isActive()){return false;}
        if($contrainte->isValideeCommission()){return false;}
        return true;
    }

    private function assertInvalider(?ContrainteCursusEtudiant $contrainte) : bool
    {
        if(!isset($contrainte)){return false;}
        if(!$contrainte->isActive()){return false;}
        if(!$contrainte->isValideeCommission()){return false;}
        return true;
    }

}
