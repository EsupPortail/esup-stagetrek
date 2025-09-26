<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenApp\Traits\MessageAwareInterface;
use UnicaenUtilisateur\Entity\Db\Role;

/**
 *
 */
class MessageInfo implements ResourceInterface
{
    /**
     *
     */
    const RESOURCE_ID = 'MessageInfo';

    const INFO = MessageAwareInterface::INFO;
    const SUCCESS = MessageAwareInterface::SUCCESS;
    const WARNING = MessageAwareInterface::WARNING;
    const ERROR =  MessageAwareInterface::ERROR;
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use HasIdTrait;

    /**
     * @var string|null $title
     */
    protected ?string $title = null;
    /**
     * @var string|null
     */
    protected ?string $message = null;
    /**
     * @var string|null
     */
    protected ?string $priority = null;
    /**
     * @var bool
     */
    protected bool $actif = true;
    /**
     * @var \DateTime|null
     */
    protected ?DateTime $dateMessage = null;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $roles;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return void
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string|null $priority
     * @return void
     */
    public function setPriority(?string $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return bool
     */
    public function isActif(): bool
    {
        return $this->actif;
    }

    /**
     * @param bool $actif
     * @return void
     */
    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }

    /**
     * Set dateMessage.
     *
     * @param \DateTime|null $dateMessage
     *
     * @return MessageInfo
     */
    public function setDateMessage(?DateTime $dateMessage = null) : static
    {
        if(!isset($dateMessage)){
            $dateMessage = new DateTime();
            $dateMessage->setTime(0,0);
        }
        $this->dateMessage = $dateMessage;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateMessage(): ?DateTime
    {
        if(!isset($this->dateMessage)){
            $this->dateMessage = new DateTime();
            $this->dateMessage->setTime(0,0);
        }
        return $this->dateMessage;
    }
    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPriorityOrder() : int
    {
        return match ($this->getPriority()) {
            MessageInfo::ERROR => 0,
            MessageInfo::WARNING => 1,
            MessageInfo::SUCCESS => 2,
            MessageInfo::INFO => 3,
            default => -1,
        };
    }


    /**
     * @param array|\Doctrine\Common\Collections\Collection $messages
     * @param string $order
     * @return array
     */
    public static function sort(array|Collection $messages, string $order = 'asc'): array
    {
        $ordre = ($order != 'desc') ? 1 : -1;
        if($messages instanceof Collection){$messages = $messages->toArray();}
        usort($messages, function (MessageInfo $m1, MessageInfo $m2) use ($ordre){
            //Message actif prioritaire
            if($m1->isActif() && ! $m2->isActif()){return -$ordre;}
            if($m2->isActif() && ! $m1->isActif()){return $ordre;}
//            Priorité
            if($m1->getPriorityOrder() != $m2->getPriorityOrder()){
                return $ordre*($m1->getPriorityOrder()-$m2->getPriorityOrder());
            }
//            date du messages
            $d1 = $m1->getDateMessage()->getTimestamp();
            $d2 = $m2->getDateMessage()->getTimestamp();
            if($d1 != $d2){
                return $ordre*($d1-$d2);
            }
            return 0;
        });
        return $messages;
    }

    /**
     * Add role.
     *
     * @param  \UnicaenUtilisateur\Entity\Db\Role $role
     *
     * @return MessageInfo
     */
    public function addRole( Role $role) : static
    {
        if(!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove role.
     *
     * @param \UnicaenUtilisateur\Entity\Db\Role $role
     * @return \Application\Entity\Db\MessageInfo
     */
    public function removeRole(Role $role) : static
    {
        $this->roles->removeElement($role);
        return $this;
    }

    /**
     * Get role.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles() : Collection
    {
        return $this->roles;
    }
}
