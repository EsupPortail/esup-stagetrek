<?php

namespace Application\Service\TerrainStage\Factory;

//ToDo : adapter pour qu'il marche avec Doctrine
use Application\Service\Misc\CSVService;
use Application\Service\Stage\SessionStageService;
use Application\Service\TerrainStage\TerrainStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class TerrainStageServiceFactory
 * @package Application\Service\TerrainStage
 */
class TerrainStageServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TerrainStageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TerrainStageService
    {
        $serviceProvider = new TerrainStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $serviceProvider->setSessionStageService($sessionStageService);

        /** @var CSVService $csvService */
        $csvService = $container->get(ServiceManager::class)->get(CSVService::class);
        $serviceProvider->setCsvService($csvService);

        return $serviceProvider;
    }
}
