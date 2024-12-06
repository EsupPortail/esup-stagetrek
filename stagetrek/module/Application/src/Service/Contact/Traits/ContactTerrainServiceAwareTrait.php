<?php

namespace Application\Service\Contact\Traits;

use Application\Service\Contact\ContactTerrainService;

trait ContactTerrainServiceAwareTrait
{
    /** @var ContactTerrainService|null */
    protected ?ContactTerrainService $contactTerrainService = null;

    /**
     * @return ContactTerrainService
     */
    public function getContactTerrainService() : ContactTerrainService
    {
        return $this->contactTerrainService;
    }

    /**
     * @param ContactTerrainService $contactTerrainService
     * @return \Application\Service\Contact\Traits\ContactTerrainServiceAwareTrait
     */
    public function setContactTerrainService(ContactTerrainService $contactTerrainService): static
    {
        $this->contactTerrainService = $contactTerrainService;
        return $this;
    }
}