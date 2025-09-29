<?php

namespace Application\Form\TerrainStage\Factory;

use Application\Form\TerrainStage\Hydrator\CategorieStageHydrator;
use Application\Form\TerrainStage\Hydrator\TerrainStageHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class TerrainStageHydratorFactory
 * @package Application\Form\TerrainStage\Factory
 */
class CategorieStageHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CategorieStageHydrator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CategorieStageHydrator
    {
        $hydrator = new CategorieStageHydrator();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $hydrator->setObjectManager($entityManager);

        $hydrator->setTagService($container->get(TagService::class));

        return $hydrator;
    }

}