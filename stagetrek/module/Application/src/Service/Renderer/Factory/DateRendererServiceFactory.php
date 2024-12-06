<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Renderer\DateRendererService;
use Interop\Container\ContainerInterface;

class DateRendererServiceFactory {

    /**
     * @param ContainerInterface $container
     * @return DateRendererService
     */
    public function __invoke(ContainerInterface $container) : DateRendererService
    {
        return new DateRendererService();
    }
}
