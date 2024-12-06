<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Renderer\AdresseRendererService;
use Interop\Container\ContainerInterface;

class AdresseRendererServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return AdresseRendererService
     */
    public function __invoke(ContainerInterface $container): AdresseRendererService
    {
        return new AdresseRendererService();
    }
}
