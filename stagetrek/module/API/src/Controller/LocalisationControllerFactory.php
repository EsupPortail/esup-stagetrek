<?php

namespace API\Controller;

use API\Service\VilleApiService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LocalisationControllerFactory implements FactoryInterface
{
    /**
     * Cretae controller
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return LocalisationController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LocalisationController
    {
        $controller = new LocalisationController();

        $entityManager = $container->get(EntityManager::class);
        $controller->setObjectManager($entityManager);

        /** @var VilleApiService $villeService */
        $villeService = $container->get(VilleApiService::class);
        $controller->setVilleApiService($villeService);

        return $controller;
    }
}