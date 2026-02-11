<?php

namespace Application\Service\Affectation\Factory;

use Application\Service\Affectation\AffectationStageService;
use Application\Service\Affectation\Algorithmes\AlgoAleatoire;
use Application\Service\Affectation\Algorithmes\AlgoScoreV1;
use Application\Service\Affectation\Algorithmes\AlgoScoreV2;
use Application\Service\Affectation\ProcedureAffectationService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Stage\SessionStageService;
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


        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $serviceProvider->setSessionStageService($sessionStageService);

//        TODO : déplacer la définition en configuration
//        Transformer en un adaptater dans un module a part
        $algoConf = [
            AlgoScoreV1::getCodeAlgo() => AlgoScoreV1::class,
            AlgoScoreV2::getCodeAlgo() => AlgoScoreV2::class,
            AlgoAleatoire::getCodeAlgo() => AlgoAleatoire::class,
        ];
        $algorithmes = [];
        foreach ($algoConf as $codeProcedure => $algoClass) {
            $algorithmes[$codeProcedure] = $container->get(ServiceManager::class)->get($algoClass);
        }
        $serviceProvider->setAlgorithmes($algorithmes);
        return $serviceProvider;
    }
}
