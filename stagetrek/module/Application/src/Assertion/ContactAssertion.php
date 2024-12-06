<?php


namespace Application\Assertion;

use Application\Controller\Contact\ContactController as Controller;
use Application\Entity\Db\Contact;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ContactPrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ContactAssertion extends AbstractAssertion
{
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;

        $contact = $this->getContact();
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER =>
                true,
            Controller::ACTION_AFFICHER, Controller::ACTION_AFFICHER_INFOS
                => $this->assertAfficher($contact),
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($contact),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($contact),
            Controller::ACTION_IMPORTER => $this->assertImporter(),
            Controller::ACTION_EXPORTER => $this->assertExporter(),
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
        if ($entity instanceof ArrayRessource) {
            $contact = $entity->get(Contact::RESOURCE_ID);
        }

        return match ($privilege) {
            ContactPrivileges::CONTACT_AFFICHER =>
                $this->assertAfficher($contact),
            ContactPrivileges::CONTACT_AJOUTER =>
                $this->assertAjouter(),
            ContactPrivileges::CONTACT_MODIFIER =>
                $this->assertModifier($contact),
            ContactPrivileges::CONTACT_SUPPRIMER =>
                $this->assertSupprimer($contact),
            ContactPrivileges::CONTACT_EXPORTER =>
                $this->assertExporter(),
            ContactPrivileges::CONTACT_IMPORTER =>
                $this->assertImporter(),
            default => false,
        };
    }

    private function assertAfficher(?Contact $contact) : bool
    {
        return isset($contact);
    }

    private function assertAjouter() : bool
    {
        return true;
    }
    private function assertModifier(?Contact $contact) : bool
    {
        return isset($contact);
    }

    private function assertSupprimer(?Contact $contact) : bool
    {
        return isset($contact);
    }

    private function assertExporter() : bool
    {// Fonction non implémenté
        return false;
    }

    private function assertImporter() : bool
    {
        return true;
    }

    protected function getContact() : ?Contact
    {
        $id = intval($this->getParam('contact', 0));
        return $this->getObjectManager()->getRepository(Contact::class)->find($id);
    }
}
