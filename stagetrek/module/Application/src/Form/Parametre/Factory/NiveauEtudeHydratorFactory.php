<?php

namespace Application\Form\Parametre\Factory;

use Application\Form\Parametre\Hydrator\NiveauEtudeHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class NiveauEtudeHydratorFactory
 * @package Application\Form\Factory\Hydrator
 */
class NiveauEtudeHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NiveauEtudeHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiveauEtudeHydrator
    {
        $hydrator = new NiveauEtudeHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }

}