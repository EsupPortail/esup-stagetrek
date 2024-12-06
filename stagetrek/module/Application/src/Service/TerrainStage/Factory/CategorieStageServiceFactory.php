<?php


namespace Application\Service\TerrainStage\Factory;

use Application\Service\Misc\CSVService;
use Application\Service\TerrainStage\CategorieStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class CategorieStageServiceFactory
 * @package Application\Service\TerrainStage
 */
class CategorieStageServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CategorieStageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CategorieStageService
    {
        $serviceProvider = new CategorieStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var CSVService $csvService */
        $csvService = $container->get(ServiceManager::class)->get(CSVService::class);
        $serviceProvider->setCsvService($csvService);

        return $serviceProvider;
    }
}
