<?php

namespace Application\Service\Renderer\Traits;

use Application\Service\Renderer\AdresseRendererService;

trait AdresseRendererServiceAwareTrait {

    /** @var AdresseRendererService|null */
    private ?AdresseRendererService $adresseRendererService = null;

    /**
     * @return AdresseRendererService
     */
    public function getAdresseRendererService(): AdresseRendererService
    {
        return $this->adresseRendererService;
    }

    /**
     * @param \Application\Service\Renderer\AdresseRendererService $adresseRendererService
     * @return \Application\Service\Renderer\Traits\AdresseRendererServiceAwareTrait
     */
    public function setAdresseRendererService(AdresseRendererService $adresseRendererService): static
    {
        $this->adresseRendererService = $adresseRendererService;
        return $this;
    }

}
