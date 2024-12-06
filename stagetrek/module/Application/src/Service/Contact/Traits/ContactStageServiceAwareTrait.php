<?php

namespace Application\Service\Contact\Traits;

use Application\Service\Contact\ContactStageService;

trait ContactStageServiceAwareTrait
{
    /** @var ContactStageService|null */
    protected ?ContactStageService $contactStageService=null;

    /**
     * @return \Application\Service\Contact\ContactStageService
     */
    public function getContactStageService() : ContactStageService
    {
        return $this->contactStageService;
    }

    /**
     * @param ContactStageService $contactStageService
     * @return \Application\Service\Contact\Traits\ContactStageServiceAwareTrait
     */
    public function setContactStageService(ContactStageService $contactStageService): static
    {
        $this->contactStageService = $contactStageService;
        return $this;
    }
}