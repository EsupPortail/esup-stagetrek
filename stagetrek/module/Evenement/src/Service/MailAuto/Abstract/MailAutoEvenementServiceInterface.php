<?php

namespace Evenement\Service\MailAuto\Abstract;

use UnicaenEvenement\Service\Evenement\EvenementServiceInterface;

Interface MailAutoEvenementServiceInterface extends EvenementServiceInterface
{
    public function findEntitiesForNewEvent() : array;
}