<?php

namespace Application\Service\Renderer\Traits;

use Application\Service\Renderer\DateRendererService;

trait DateRendererServiceAwareTrait {

    /** @var DateRendererService|null */
    private ?DateRendererService $dateRendererService = null;

    /**
     * @return DateRendererService
     */
    public function getDateRendererService(): DateRendererService
    {
        return $this->dateRendererService;
    }

    /**
     * @param \Application\Service\Renderer\DateRendererService $dateRendererService
     * @return \Application\Service\Renderer\Traits\DateRendererServiceAwareTrait
     */
    public function setDateRendererService(DateRendererService $dateRendererService): static
    {
        $this->dateRendererService = $dateRendererService;
        return $this;
    }

}
