<?php

namespace Application\Form\Convention\Factory;

use Application\Form\Convention\Hydrator\ModeleConventionStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ModeleConventionStageHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModeleConventionStageHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ModeleConventionStageHydrator
    {
        $hydrator = new ModeleConventionStageHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);
        return $hydrator;
    }

}