<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Renderer\PdfRendererService;
use Interop\Container\ContainerInterface;

class PdfRendererServiceFactory {

    /**
     * @param ContainerInterface $container
     * @return \Application\Service\Renderer\PdfRendererService
     */
    public function __invoke(ContainerInterface $container) : PdfRendererService
    {
        return new PdfRendererService();
    }
}
