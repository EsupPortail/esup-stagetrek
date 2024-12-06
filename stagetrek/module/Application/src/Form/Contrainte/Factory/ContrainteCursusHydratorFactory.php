<?php

namespace Application\Form\Contrainte\Factory;

use Application\Form\Contrainte\Hydrator\ContrainteCursusHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ContrainteCursusHydratorFactory
 * @package Application\Form\ContraintesCursus\Factory
 */
class ContrainteCursusHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ContrainteCursusHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContrainteCursusHydrator
    {
        $hydrator = new ContrainteCursusHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }
}