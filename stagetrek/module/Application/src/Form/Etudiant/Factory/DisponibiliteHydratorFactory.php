<?php

namespace Application\Form\Etudiant\Factory;

use Application\Form\Etudiant\Hydrator\DisponibiliteHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class DisponibiliteHydratorFactory
 * @package Application\Form\Disponibilite
 */
class DisponibiliteHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DisponibiliteHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DisponibiliteHydrator
    {
        $hydrator = new DisponibiliteHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }

}