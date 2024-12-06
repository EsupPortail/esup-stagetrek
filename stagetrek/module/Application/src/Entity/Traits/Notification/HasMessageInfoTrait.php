<?php

namespace Application\Entity\Traits\Notification;


use Application\Entity\Db\MessageInfo;

trait HasMessageInfoTrait
{
    /** @var MessageInfo|null  $messageInfo*/
    protected ?MessageInfo $messageInfo;

    public function getMessageInfo(): ?MessageInfo
    {
        return $this->messageInfo;
    }

    public function setMessageInfo(?MessageInfo $messageInfo): void
    {
        $this->messageInfo = $messageInfo;
    }

    /**
     * @return bool
     */
    public function hasMessageInfo(): bool
    {
        return isset($this->messageInfo);
    }

}