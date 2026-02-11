<?php

namespace Indicateur\Service\Perimetre;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class  PerimetreServiceFactory
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PerimetreService
    {
        $service = new PerimetreService();
        return $service;
    }
}