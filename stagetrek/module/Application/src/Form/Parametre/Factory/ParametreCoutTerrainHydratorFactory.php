<?php

namespace Application\Form\Parametre\Factory;

use Application\Form\Parametre\Hydrator\ParametreCoutTerrainHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ParametreTerrainCoutAffectationFixeHydratorFactory
 * @package Application\Form\Factory\Hydrator
 */
class ParametreCoutTerrainHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreCoutTerrainHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreCoutTerrainHydrator
    {
        $hydrator = new ParametreCoutTerrainHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }
}