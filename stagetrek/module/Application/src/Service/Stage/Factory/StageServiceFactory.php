<?php

namespace Application\Service\Stage\Factory;

use Application\Service\Affectation\AffectationStageService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Stage\SessionStageService;
use Application\Service\Stage\StageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;

/**
 * Class SessionStageServiceFactory
 * @package Application\Service\Stage
 */
class StageServiceFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return StageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): StageService
    {
        $serviceProvider = new StageService();
        /** @var SessionStageService $sessionStageService */
        //On ne pas ici par la factory pour éviter une boucle infini
        $sessionStageService = new SessionStageService();

        $serviceProvider->setSessionStageService($sessionStageService);
        $sessionStageService->setStageService($serviceProvider);

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);
        $sessionStageService->setObjectManager($entityManager);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);
        $sessionStageService->setEtudiantService($etudiantService);

        $sessionStageService->setStageService($serviceProvider);
        $sessionStageService->setEtudiantService($etudiantService);

        $serviceProvider->setSessionStageService($sessionStageService);

        /** @var \Application\Service\Affectation\AffectationStageService $affectationStageService */
        $affectationStageService = new AffectationStageService();
        $affectationStageService->setObjectManager($entityManager);
        $affectationStageService->setStageService($serviceProvider);
        $affectationStageService->setSessionStageService($sessionStageService);
        $affectationStageService->setEtudiantService($etudiantService);

        $sessionStageService->setAffectationStageService($affectationStageService);


        $etatTypeService = $container->get(EtatTypeService::class);
        $etatTypeInstanceService = $container->get(EtatInstanceService::class);
        $serviceProvider->setEtatTypeService($etatTypeService);
        $serviceProvider->setEtatInstanceService($etatTypeInstanceService);
        $sessionStageService->setEtatTypeService($etatTypeService);
        $sessionStageService->setEtatInstanceService($etatTypeInstanceService);
        $affectationStageService->setEtatTypeService($etatTypeService);
        $affectationStageService->setEtatInstanceService($etatTypeInstanceService);
        return $serviceProvider;
    }
}