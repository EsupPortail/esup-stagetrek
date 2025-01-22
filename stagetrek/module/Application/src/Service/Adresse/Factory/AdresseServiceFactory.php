<?php

namespace Application\Service\Adresse\Factory;

use Application\Service\Adresse\AdresseService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdresseServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AdresseService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AdresseService
    {
        $service = new AdresseService();
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);
        return $service;
    }
}
