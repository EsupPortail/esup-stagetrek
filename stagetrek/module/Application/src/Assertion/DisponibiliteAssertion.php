<?php


namespace Application\Assertion;

use Application\Controller\Etudiant\DisponibiliteController as Controller;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\EtudiantPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class DisponibiliteAssertion extends AbstractAssertion
{
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $etudiant = $this->getEtudiant();
        $disponibilite = $this->getDisponibilite();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_LISTER => $this->assertLister($etudiant),
            Controller::ACTION_AJOUTER =>$this->assertAjouter($etudiant),
            Controller::ACTION_MODIFIER => $this->assertModifier($disponibilite),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($disponibilite),
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
        $etudiant = ($entity instanceof Etudiant) ? $entity : null;
        $disponibilite = ($entity instanceof Disponibilite) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $etudiant = $entity->get(Etudiant::RESOURCE_ID);
            $disponibilite = $entity->get(Disponibilite::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::DISPONIBILITE_AFFICHER => $this->assertLister($etudiant),
            Privilege::DISPONIBILITE_AJOUTER => $this->assertAjouter($etudiant),
            Privilege::DISPONIBILITE_MODIFIER => $this->assertModifier($disponibilite),
            Privilege::DISPONIBILITE_SUPPRIMER => $this->assertSupprimer($disponibilite),
            default => false,
        };
    }

    private function assertLister(?Etudiant $etudiant) : bool
    {
        return isset($etudiant);
    }

    private function assertAjouter(?Etudiant $etudiant) : bool
    {
        return isset($etudiant);
    }

    private function assertModifier(?Disponibilite $disponibilite) : bool
    {
        return isset($disponibilite);
    }

    private function assertSupprimer(?Disponibilite $disponibilite) : bool
    {
        return isset($disponibilite);
    }

    protected function getDisponibilite() : ?Disponibilite
    {
        $id = intval($this->getParam('disponibilite', 0));
        return $this->getObjectManager()->getRepository(Disponibilite::class)->find($id);
    }

    protected function getEtudiant() : ?Etudiant
    {
        $id = intval($this->getParam('etudiant', 0));
        return $this->getObjectManager()->getRepository(Etudiant::class)->find($id);
    }

}
