<?php

namespace Application\Service\Renderer\Traits;

use Application\Service\Renderer\UrlService;

trait UrlServiceAwareTrait {

    /** @var UrlService|null */
    private ?UrlService $urlService = null;

    /**
     * @return UrlService
     */
    public function getUrlService(): UrlService
    {
        return $this->urlService;
    }

    /**
     * @param UrlService $urlService
     * @return \Application\Service\Renderer\Traits\UrlServiceAwareTrait
     */
    public function setUrlService(UrlService $urlService): static
    {
        $this->urlService = $urlService;
        return $this;
    }
}
