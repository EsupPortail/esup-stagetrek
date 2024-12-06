<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Renderer\ContactRendererService;
use Interop\Container\ContainerInterface;

class ContactRendererServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return ContactRendererService
     */
    public function __invoke(ContainerInterface $container): ContactRendererService
    {
        return new ContactRendererService();
    }
}
