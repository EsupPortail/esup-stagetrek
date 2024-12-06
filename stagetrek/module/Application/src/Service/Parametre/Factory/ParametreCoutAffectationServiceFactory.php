<?php

namespace Application\Service\Parametre\Factory;

//ToDo : adapter pour qu'il marche avec Doctrine
use Application\Service\Parametre\ParametreCoutAffectationService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ParametreServiceFactory
 * @package Application\Service\Parametre
 */
class ParametreCoutAffectationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutAffectationService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutAffectationService
    {
        $service = new ParametreCoutAffectationService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $service->setObjectManager($entityManager);


        return $service;
    }
}
