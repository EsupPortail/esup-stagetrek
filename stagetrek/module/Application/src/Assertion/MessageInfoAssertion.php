<?php


namespace Application\Assertion;

use Application\Controller\Notification\MessageInfoController as Controler;
use Application\Entity\Db\MessageInfo;
use Application\Misc\ArrayRessource;
use Application\Provider\Privilege\MessagePrivilege as Privilege;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

class MessageInfoAssertion extends AbstractAssertion
{
    use UserServiceAwareTrait;

   protected function assertController($controller, $action = null, $privilege = null)
    {
        $role = $this->getRole();

        $messageInfo = $this->getMessageInfo();
        // si le rôle n'est pas renseigné
        if (!$role instanceof RoleInterface) return false;
        return match ($action) {
            Controler::ACTION_INDEX, Controler::ACTION_LISTER => true,
            Controler::ACTION_AJOUTER => $this->assertAjouter(),
            Controler::ACTION_MODIFIER => $this->assertModifier($messageInfo),
            Controler::ACTION_SUPPRIMER => $this->assertSupprimer($messageInfo),
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
        $messageInfo = ($entity instanceof MessageInfo) ? $entity : null;
        if ($entity instanceof ArrayRessource) {
            $messageInfo = $entity->get(MessageInfo::RESOURCE_ID);
        }
        return match ($privilege) {
            Privilege::MESSAGE_INFO_AFFICHER => true,
            Privilege::MESSAGE_INFO_AJOUTER => $this->assertAjouter(),
            Privilege::MESSAGE_INFO_MODIFIER => $this->assertModifier($messageInfo),
            Privilege::MESSAGE_INFO_SUPPRIMER => $this->assertSupprimer($messageInfo),
            default => false,
        };
    }

    private function assertAjouter() : bool
    {
        return true;
    }

    private function assertModifier(?MessageInfo $messageInfo) : bool
    {
        return isset($messageInfo);
    }

    private function assertSupprimer(?MessageInfo $messageInfo) : bool
    {
        return isset($messageInfo);
    }

    protected function getMessageInfo() : ?MessageInfo
    {
        $id = intval($this->getParam('messageInfo', 0));
        return $this->getObjectManager()->getRepository(MessageInfo::class)->find($id);
    }

}
