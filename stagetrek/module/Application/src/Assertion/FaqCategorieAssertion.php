<?php


namespace Application\Assertion;

use Application\Controller\Notification\FaqCategorieController as Controller;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\FaqPrivileges as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class FaqCategorieAssertion extends AbstractAssertion
{

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $faqCategorieQuestion = $this->getFaqCategorieQuestion();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controller::ACTION_INDEX, Controller::ACTION_LISTER => true,
            Controller::ACTION_AJOUTER => $this->assertAjouter(),
            Controller::ACTION_MODIFIER => $this->assertModifier($faqCategorieQuestion),
            Controller::ACTION_SUPPRIMER => $this->assertSupprimer($faqCategorieQuestion),
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
        $faqCategorieQuestion = ($entity instanceof FaqCategorieQuestion) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $faqCategorieQuestion = $entity->get(FaqCategorieQuestion::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::FAQ_CATEGORIE_AFFICHER => true,
            Privilege::FAQ_CATEGORIE_AJOUTER => $this->assertAjouter(),
            Privilege::FAQ_CATEGORIE_MODIFIER => $this->assertModifier($faqCategorieQuestion),
            Privilege::FAQ_CATEGORIE_SUPPRIMER => $this->assertSupprimer($faqCategorieQuestion),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?FaqCategorieQuestion $faqCategorieQuestion) : bool
    {
        return isset($faqCategorieQuestion);
    }

    private function assertSupprimer(?FaqCategorieQuestion $faqCategorieQuestion) : bool
    {
        if(!isset($faqCategorieQuestion)){return false;}
        if(!$faqCategorieQuestion->getQuestions()->isEmpty()){return false;}
        return true;
    }

    protected function getFaqCategorieQuestion() : ?FaqCategorieQuestion
    {
        $id = intval($this->getParam('faqCategorieQuestion', 0));
        return $this->getObjectManager()->getRepository(FaqCategorieQuestion::class)->find($id);
    }

}
