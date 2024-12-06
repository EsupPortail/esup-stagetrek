<?php

namespace Application\Form\Referentiel\Factory;

use Application\Form\Referentiel\Hydrator\ReferentielPromoHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ReferentielPormoHydratorFactory
 * @package Application\Form\Factory\Hydrator
 */
class ReferentielPormoHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\Form\Referentiel\Hydrator\ReferentielPromoHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ReferentielPromoHydrator
    {
        $hydrator = new ReferentielPromoHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        return $hydrator;
    }

}