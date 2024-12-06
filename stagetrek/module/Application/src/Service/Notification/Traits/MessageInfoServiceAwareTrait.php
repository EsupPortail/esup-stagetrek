<?php

namespace Application\Service\Notification\Traits;

use Application\Service\Notification\MessageInfoService;

/**
 * Traits MessageInfoServiceAwareTrait
 * @package Application\Service\Messages
 */
Trait MessageInfoServiceAwareTrait
{
    /** @var MessageInfoService|null $messageInfoService */
    protected ?MessageInfoService $messageInfoService=null;

    /**
     * @return MessageInfoService
     */
    public function getMessageInfoService(): MessageInfoService
    {
        return $this->messageInfoService;
    }

    /**
     * @param MessageInfoService $messageInfoService
     * @return MessageInfoServiceAwareTrait
     */
    public function setMessageInfoService(MessageInfoService $messageInfoService): static
    {
        $this->messageInfoService = $messageInfoService;
        return $this;
    }



}