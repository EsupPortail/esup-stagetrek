<?php

namespace Application\Service\Parametre\Factory;

//ToDo : adapter pour qu'il marche avec Doctrine
use Application\Service\Parametre\ParametreCoutTerrainService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ParametreCoutTerrainServiceFactory
 * @package Application\Service\Parametre
 */
class ParametreCoutTerrainServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutTerrainService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutTerrainService
    {
        $service = new ParametreCoutTerrainService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);


        return $service;
    }
}
