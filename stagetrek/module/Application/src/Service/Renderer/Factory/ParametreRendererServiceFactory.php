<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Parametre\ParametreService;
use Application\Service\Renderer\ParametreRendererService;
use Application\Service\Renderer\PdfRendererService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;

class ParametreRendererServiceFactory {

    /**
     * @param ContainerInterface $container
     * @return \Application\Service\Renderer\PdfRendererService
     */
    public function __invoke(ContainerInterface $container) : ParametreRendererService
    {
        $serviceProvider = new ParametreRendererService();

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        return $serviceProvider;

    }
}
