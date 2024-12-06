<?php

namespace Application\Provider\Notification;

use UnicaenApp\Traits\MessageAwareInterface;

/**
 * Liste des privilèges utilisables.
 */
class MessageInfoProvider
{
    const INFO = MessageAwareInterface::INFO;
    const SUCCESS = MessageAwareInterface::SUCCESS;
    const WARNING = MessageAwareInterface::WARNING;
    const ERROR =  MessageAwareInterface::ERROR;
}