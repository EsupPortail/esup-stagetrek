<?php

namespace Application\Service\Affectation\Factory;

use Application\Provider\ProcedureAffectation\ProcedureAffectationProvider;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Affectation\Algorithmes\AlgoAleatoire;
use Application\Service\Affectation\Algorithmes\AlgoScoreV1;
use Application\Service\Affectation\Algorithmes\AlgoScoreV2;
use Application\Service\Affectation\ProcedureAffectationService;
use Application\Service\Parametre\ParametreService;
use Application\Service\TerrainStage\TerrainStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PreferenceServiceFactory
 * @package Application\Service\Preference
 */
class AlgoAleatoireFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ProcedureAffectationService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AlgoAleatoire
    {
        $serviceProvider = new AlgoAleatoire();
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        /** @var AffectationStageService $affectationStageService */
        $affectationStageService = $container->get(ServiceManager::class)->get(AffectationStageService::class);
        $serviceProvider->setAffectationStageService($affectationStageService);

        /** @var TerrainStageService $terrainStageService */
        $terrainStageService = $container->get(ServiceManager::class)->get(TerrainStageService::class);
        $serviceProvider->setTerrainStageService($terrainStageService);

        return $serviceProvider;
    }
}
