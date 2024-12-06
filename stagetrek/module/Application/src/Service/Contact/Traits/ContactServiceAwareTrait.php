<?php

namespace Application\Service\Contact\Traits;

use Application\Service\Contact\ContactService;

trait ContactServiceAwareTrait
{
    /** @var ContactService|null */
    protected ?ContactService $contactService = null;

    /**
     * @return ContactService
     */
    public function getContactService() : ContactService
    {
        return $this->contactService;
    }

    /**
     * @param ContactService $contactService
     * @return \Application\Service\Contact\Traits\ContactServiceAwareTrait
     */
    public function setContactService(ContactService $contactService) : static
    {
        $this->contactService = $contactService;
        return $this;
    }
}