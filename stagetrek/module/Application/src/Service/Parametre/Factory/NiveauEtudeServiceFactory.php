<?php

namespace Application\Service\Parametre\Factory;

use Application\Service\Parametre\NiveauEtudeService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class NiveauEtudeServiceFactory
 * @package Application\Service\AnneeUniversitaire
 */
class NiveauEtudeServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NiveauEtudeService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiveauEtudeService
    {
        $service = new NiveauEtudeService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);

        return $service;
    }
}
