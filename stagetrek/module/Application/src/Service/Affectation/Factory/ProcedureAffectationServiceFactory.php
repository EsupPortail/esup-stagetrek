<?php

namespace Application\Service\Affectation\Factory;

use Application\Provider\ProcedureAffectation\ProcedureAffectationProvider;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Affectation\Algorithmes\AlgoAleatoire;
use Application\Service\Affectation\Algorithmes\AlgoScoreV1;
use Application\Service\Affectation\Algorithmes\AlgoScoreV2;
use Application\Service\Affectation\ProcedureAffectationService;
use Application\Service\Parametre\ParametreService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PreferenceServiceFactory
 * @package Application\Service\Preference
 */
class ProcedureAffectationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ProcedureAffectationService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : ProcedureAffectationService
    {
        $serviceProvider = new ProcedureAffectationService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        /** @var AffectationStageService $affectationStageService */
        $affectationStageService = $container->get(ServiceManager::class)->get(AffectationStageService::class);
        $serviceProvider->setAffectationStageService($affectationStageService);

//        TODO : déplacer la définition en configuration
        $algoConf = [
            ProcedureAffectationProvider::ALGO_SCORE_V1 => AlgoScoreV1::class,
            ProcedureAffectationProvider::ALGO_SCORE_V2 => AlgoScoreV2::class,
            ProcedureAffectationProvider::ALGO_ALEATOIRE => AlgoAleatoire::class,
        ];
        $algorithmes = [];
        foreach ($algoConf as $codeProcedure => $algoClass) {
            $algorithmes[$codeProcedure] = $container->get(ServiceManager::class)->get($algoClass);
        }
        $serviceProvider->setAlgorithmes($algorithmes);
        return $serviceProvider;
    }
}
