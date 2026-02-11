<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Renderer\ContactRendererService;
use Application\Service\Renderer\UrlService;
use Interop\Container\ContainerInterface;

class ContactRendererServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return ContactRendererService
     */
    public function __invoke(ContainerInterface $container): ContactRendererService
    {
        $service = new ContactRendererService();
        $service->setUrlService($container->get(UrlService::class));
        return $service;
    }
}
