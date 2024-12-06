<?php


namespace Application\Form\Notification\Hydrator;

use Application\Entity\Db\MessageInfo;
use Application\Form\Notification\Fieldset\MessageInfoFieldset;
use Application\Provider\Notification\MessageInfoProvider;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 * Class MessageInfoHydrator
 * @package Application\Form\Hydrator
 */
class MessageInfoHydrator extends AbstractHydrator implements HydratorInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        /** @var MessageInfo $message */
        $message = $object;
        $data = [];

        $data[MessageInfoFieldset::INPUT_TITLE] = ($message->getTitle()) ?? null;
        $data[MessageInfoFieldset::INPUT_MESSAGE] = ($message->getMessage()) ?? null;
        $data[MessageInfoFieldset::INPUT_PRIORITY] = ($message->getPriority()) ?? MessageInfoProvider::INFO;
        $data[MessageInfoFieldset::INPUT_ACTIF] = ($message->isActif()) ? 1 : 0;
        $today = new DateTime();
        $data[MessageInfoFieldset::INPUT_DATE] = ($message->getDateMessage()) ? $message->getDateMessage()->format('Y-m-d') : $today->format('Y-m-d');

        if ($message->getRoles()->isEmpty()) {
            $data[MessageInfoFieldset::INPUT_ROLES] = [];
        } else {
            foreach ($message->getRoles() as $r) {
                $data[MessageInfoFieldset::INPUT_ROLES][] = $r->getId();
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param object $object
     * @return MessageInfo
     */
    public function hydrate(array $data, object $object): MessageInfo
    {
        /** @var MessageInfo $message */
        $message = $object;

        if (isset($data[MessageInfoFieldset::INPUT_TITLE])) {
            $message->setTitle(trim($data[MessageInfoFieldset::INPUT_TITLE]));
        }
        if (isset($data[MessageInfoFieldset::INPUT_MESSAGE])) {
            $message->setMessage(trim($data[MessageInfoFieldset::INPUT_MESSAGE]));
        }
        if (isset($data[MessageInfoFieldset::INPUT_PRIORITY])) {
            $message->setPriority($data[MessageInfoFieldset::INPUT_PRIORITY]);
        }

        if ($data[MessageInfoFieldset::INPUT_DATE]) {
            $date = new DateTime();
            $chaine = explode("-", $data[MessageInfoFieldset::INPUT_DATE]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if (!$message->getDateMessage()
                || $message->getDateMessage()->getTimestamp() != $date->getTimestamp()) {
                $message->setDateMessage($date);
            }
        } else {
            $message->setDateMessage();
        }

        if (isset($data[MessageInfoFieldset::INPUT_ACTIF])) {
            $message->setActif(boolval($data[MessageInfoFieldset::INPUT_ACTIF]));
        }

        if (key_exists(MessageInfoFieldset::INPUT_ROLES, $data)) {
            $rolesSelected = $data[MessageInfoFieldset::INPUT_ROLES];
            /** @var Role[] $roles */
            $roles = $this->getObjectManager()->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function (Role $r) use ($rolesSelected) {
                return in_array($r->getId(), $rolesSelected);
            });
            $message->getRoles()->clear();
            foreach ($roles as $r) {
                $message->addRole($r);
            }
        } else {
            $message->getRoles()->clear();
        }
        return $message;
    }
}