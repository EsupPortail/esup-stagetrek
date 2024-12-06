<?php

namespace Application\Service\Renderer\Traits;

use Application\Service\Renderer\DateRendererService;
use Application\Service\Renderer\ParametreRendererService;
use Application\Service\Renderer\PdfRendererService;

trait ParametreRendererServiceAwareTrait {

    /** @var ParametreRendererService|null */
    private ?ParametreRendererService $parametreRendererService = null;

    public function getParametreRendererService(): ?ParametreRendererService
    {
        return $this->parametreRendererService;
    }

    public function setParametreRendererService(?ParametreRendererService $parametreRendererService): void
    {
        $this->parametreRendererService = $parametreRendererService;
    }

}
