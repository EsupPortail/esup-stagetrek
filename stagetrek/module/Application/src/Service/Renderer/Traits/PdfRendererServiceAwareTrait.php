<?php

namespace Application\Service\Renderer\Traits;

use Application\Service\Renderer\PdfRendererService;
use Application\Service\Renderer\DateRendererService;

trait PdfRendererServiceAwareTrait {

    /** @var DateRendererService|null */
    private ?PdfRendererService $pdfRendererService = null;

    public function getPdfRendererService(): ?PdfRendererService
    {
        return $this->pdfRendererService;
    }

    public function setPdfRendererService(?PdfRendererService $pdfRendererService): void
    {
        $this->pdfRendererService = $pdfRendererService;
    }


}
