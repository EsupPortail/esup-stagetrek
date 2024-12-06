<?php


namespace Application\Assertion;

use Application\Controller\Parametre\NiveauEtudeController as Controller;
use Application\Entity\Db\NiveauEtude;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ParametrePrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class NiveauEtudeAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $niveau = $this->getNiveauEtude();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($niveau),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($niveau),
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
        $niveau = ($entity instanceof NiveauEtude) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $niveau = $entity->get(NiveauEtude::RESOURCE_ID);
        }
        return match ($privilege) {
            ParametrePrivileges::NIVEAU_ETUDE_AFFICHER => true,
            ParametrePrivileges::NIVEAU_ETUDE_AJOUTER => $this->assertAjouter(),
            ParametrePrivileges::NIVEAU_ETUDE_MODIFIER => $this->assertModifier($niveau),
            ParametrePrivileges::NIVEAU_ETUDE_SUPPRIMER => $this->assertSupprimer($niveau),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?NiveauEtude $niveau) : bool
    {
        return isset($niveau);
    }


    protected function assertSupprimer(?NiveauEtude $niveau): bool
    {
        if(!isset($niveau)){return false;}
        if($niveau->getNiveauEtudeSuivant()){return false;}
        $groupes = $niveau->getGroupes()->toArray();
        if(!empty($groupes)){return false;}
        $contraintes = $niveau->getTerrainsContraints()->toArray();
        if(!empty($contraintes)){return false;}
        return true;
    }

    protected function getNiveauEtude() : ?NiveauEtude
    {
        $id = intval($this->getParam('niveauEtude', 0));
        return $this->getObjectManager()->getRepository(NiveauEtude::class)->find($id);
    }

}