<?php


namespace Application\Assertion;

use Application\Controller\Contact\ContactTerrainController as Controller;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\TerrainStage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ContactPrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ContactTerrainAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null): bool
   {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        $contact = $this->getContact();
        $contactTerrain = $this->getContactTerrain();
        $terrain = $this->getTerrainStage();
        return match ($action) {
            Controller::ACTION_AFFICHER => $this->assertAfficher($contact),
            Controller::ACTION_LISTER => $this->assertLister($contact),
            Controller::ACTION_AJOUTER => $this->assertAjouter($contact, $terrain),
            Controller::ACTION_MODIFIER => $this->assertModifier($contactTerrain),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($contactTerrain),
            Controller::ACTION_IMPORTER => $this->assertImporter(),
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


        $contact = ($entity instanceof Contact) ? $entity : null;
        $terrain = ($entity instanceof TerrainStage) ? $entity : null;
        $contactTerrain = ($entity instanceof ContactTerrain) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $contact = $entity->get(Contact::RESOURCE_ID);
            $contactTerrain = $entity->get(TerrainStage::RESOURCE_ID);
            $terrain = $entity->get(TerrainStage::RESOURCE_ID);
        }

        return match ($privilege) {
            ContactPrivileges::CONTACT_TERRAIN_AFFICHER =>
                $this->assertAfficher($contact),
            ContactPrivileges::CONTACT_TERRAIN_AJOUTER =>
            $this->assertAjouter($contact, $terrain),
            ContactPrivileges::CONTACT_TERRAIN_MODIFIER =>
            $this->assertModifier($contactTerrain),
            ContactPrivileges::CONTACT_TERRAIN_SUPPRIMER =>
            $this->assertSupprimer($contactTerrain),
            ContactPrivileges::CONTACT_TERRAIN_IMPORTER =>
            $this->assertImporter(),
            default => false,
        };
    }

    private function assertAfficher(?ContactTerrain $contactTerrain): bool
    {
        return isset($contactTerrain);
    }

    private function assertLister(?Contact $contactTerrain): bool
    {
        return isset($contactTerrain);
    }

    protected function assertAjouter(?Contact $contact, ?TerrainStage $terrainStage) : bool
    {
        return (isset($contact)||isset($terrainStage));

    }

private function assertModifier(?ContactTerrain $contactTerrain) : bool
    {
        return isset($contactTerrain);
    }

    private function assertSupprimer(?ContactTerrain $contactTerrain) : bool
    {
        return isset($contactTerrain);
    }

    protected function getContact() : ?Contact
    {
        $id = intval($this->getParam('contact', 0));
        return $this->getObjectManager()->getRepository(Contact::class)->find($id);
    }

    protected function getContactTerrain() : ?ContactTerrain
    {
        $id = intval($this->getParam('contactTerrain', 0));
        return $this->getObjectManager()->getRepository(ContactTerrain::class)->find($id);
    }

    protected function getTerrainStage() : ?TerrainStage
    {
        $id = intval($this->getParam('terrainStage', 0));
        return $this->getObjectManager()->getRepository(TerrainStage::class)->find($id);
    }

    private function assertImporter() : bool
    {
        return true;
    }
}
