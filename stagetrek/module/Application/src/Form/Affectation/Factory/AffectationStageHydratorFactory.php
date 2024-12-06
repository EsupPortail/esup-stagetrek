<?php

namespace Application\Form\Affectation\Factory;

use Application\Form\Affectation\Hydrator\AffectationStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AffectationStageHydratorFactory
 * @package Application\Form\AffectationStage\Factory
 */
class AffectationStageHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AffectationStageHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AffectationStageHydrator
    {
        $hydrator = new AffectationStageHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }
}
