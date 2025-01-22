<?php


namespace Application\Assertion;

use Application\Controller\Contact\ContactStageController as Controller;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\ContactPrivileges;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class ContactStageAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;

        $contact = $this->getContact();
        $contactStage = $this->getContactStage();
        $stage = $this->getStage();
        return match ($action) {
            Controller::ACTION_LISTER => $this->assertLister($contact),
            Controller::ACTION_AJOUTER => $this->assertAjouter($contact, $stage),
            Controller::ACTION_MODIFIER => $this->assertModifier($contactStage),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($contactStage),
            Controller::ACTION_SEND_MAIL_VALIDATION => $this->assertSendMailValidation($contactStage),
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
        $contactStage = ($entity instanceof ContactStage) ? $entity : null;
        $stage = ($entity instanceof Stage) ? $entity : null;

        if ($entity instanceof ArrayRessource) {
            $contact = $entity->get(Contact::RESOURCE_ID);
            $contactStage = $entity->get(ContactStage::RESOURCE_ID);
            $stage = $entity->get(Stage::RESOURCE_ID);
        }
        return match ($privilege) {
            ContactPrivileges::CONTACT_STAGE_AJOUTER =>
                $this->assertAjouter($contact, $stage),
            ContactPrivileges::CONTACT_TERRAIN_MODIFIER =>
                $this->assertModifier($contactStage),
            ContactPrivileges::CONTACT_SUPPRIMER =>
                $this->assertSupprimer($contactStage),
            ContactPrivileges::SEND_MAIL_VALIDATION =>
                $this->assertSendMailValidation($contactStage),
            default => false,
        };
    }

    private function assertLister(?Contact $contact): bool
    {
        return isset($contact);
    }

    protected function assertAjouter(?Contact $contact, ?Stage $stage) : bool
    {
        return (isset($contact)||isset($stage));

    }
    private function assertModifier(?ContactStage $contact) : bool
    {
        return isset($contact);
    }

    private function assertSupprimer(?ContactStage $contact) : bool
    {
        return isset($contact);
    }

    protected function assertSendMailValidation(?ContactStage $contactStage) : bool
    {
        if(!isset($contactStage)){return false;}
        if(!$contactStage->isActif()){return false;}
        if(!$contactStage->canValiderStage()){return false;}

        $stage = $contactStage->getStage();
        if(!isset($stage)){return false;}
        //Désactivé car on peux vouloir envoyer un liens même en dehors de la phase de validation
      //  if(!$stage->hasEtatPhaseValidation() && !$stage->hasEtatValidationEnRetard()){return false;}

        $affectation = $stage->getAffectationStage();
        if(!$affectation || !$affectation->hasEtatValidee()){return false;}

        $mail = $contactStage->getEmail();
        if(!$mail || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {return false;}

        $validationStage = $stage->getValidationStage();
        if(!$validationStage){return false;}
        //Le stage ne doit pas avoir déjà été valider/invalider
        if($validationStage->validationEffectue()){return false;}

        return true;
    }

    protected function getContact() : ?Contact
    {
        $id = intval($this->getParam('contact', 0));
        return $this->getObjectManager()->getRepository(Contact::class)->find($id);
    }

    protected function getContactStage() : ?ContactStage
    {
        $id = intval($this->getParam('contactStage', 0));
        return $this->getObjectManager()->getRepository(ContactStage::class)->find($id);
    }

    protected function getStage() : ?Stage
    {
        $id = intval($this->getParam('stage', 0));
        return $this->getObjectManager()->getRepository(Stage::class)->find($id);
    }
}
