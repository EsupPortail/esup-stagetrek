<?php

namespace Application\Service\Renderer\Traits;

use Application\Service\Renderer\ContactRendererService;

trait ContactRendererServiceAwareTrait {

    /** @var ContactRendererService|null */
    protected ?ContactRendererService $contactRendererService = null;

    /**
     * @return ContactRendererService
     */
    public function getContactRendererService(): ContactRendererService
    {
        return $this->contactRendererService;
    }

    public function setContactRendererService(ContactRendererService $contactRendererService): static
    {
        $this->contactRendererService = $contactRendererService;
        return $this;
    }

}
