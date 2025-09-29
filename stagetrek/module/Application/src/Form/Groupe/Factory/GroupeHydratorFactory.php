<?php

namespace Application\Form\Groupe\Factory;

use Application\Form\Groupe\Hydrator\GroupeHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class GroupeHydratorFactory
 * @package Application\Form\Groupe\Factory\
 */
class GroupeHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GroupeHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GroupeHydrator
    {
        $hydrator = new GroupeHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        $hydrator->setTagService($container->get(TagService::class));

        return $hydrator;
    }

}