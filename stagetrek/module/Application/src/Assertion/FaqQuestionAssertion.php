<?php


namespace Application\Assertion;

use Application\Controller\Notification\FaqQuestionController as Controller;
use Application\Entity\Db\Faq;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\FaqPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class FaqQuestionAssertion extends AbstractAssertion
{
   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $faq = $this->getFaq();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($faq),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($faq),
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
        $faq = ($entity instanceof Faq) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $faq = $entity->get(Faq::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::FAQ_QUESTION_AFFICHER => true,
            Privilege::FAQ_QUESTION_AJOUTER => $this->assertAjouter(),
            Privilege::FAQ_QUESTION_MODIFIER => $this->assertModifier($faq),
            Privilege::FAQ_QUESTION_SUPPRIMER => $this->assertSupprimer($faq),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?Faq $faq) : bool
    {
        return isset($faq);
    }

    private function assertSupprimer(?Faq $faq) : bool
    {
        return isset($faq);
    }

    protected function getFaq() : ?Faq
    {
        $id = intval($this->getParam('faq', 0));
        return $this->getObjectManager()->getRepository(Faq::class)->find($id);
    }


}
