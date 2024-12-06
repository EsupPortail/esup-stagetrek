<?php

namespace Application\Service\Affectation\Factory;

use Application\Service\Affectation\AffectationStageService;
use Application\Service\Contact\ContactStageService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Stage\SessionStageService;
use Application\Service\Stage\StageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;

/**
 * Class AffectationStageServiceFactory
 * @package Application\Service\EntityService\Factory
 */
class AffectationStageServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AffectationStageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AffectationStageService
    {
        $serviceProvider = new AffectationStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);


        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $serviceProvider->setStageService($stageService);

        /** @var ContactStageService $contactStageService */
        $contactStageService = $container->get(ServiceManager::class)->get(ContactStageService::class);
        $serviceProvider->setContactStageService($contactStageService);


        $etatTypeService = $container->get(ServiceManager::class)->get(EtatTypeService::class);
        $etatInstanceService = $container->get(ServiceManager::class)->get(EtatInstanceService::class);
        $serviceProvider->setEtatTypeService($etatTypeService);
        $serviceProvider->setEtatInstanceService($etatInstanceService);

        //On ne passe pas par la factory pour éviter les boucles infinie
        $sessionStageService = new SessionStageService();
        $sessionStageService->setObjectManager($entityManager);
        $sessionStageService->setEtudiantService($etudiantService);
        $sessionStageService->setStageService($stageService);
        $sessionStageService->setEtatTypeService($etatTypeService);
        $sessionStageService->setEtatInstanceService($etatInstanceService);
        $sessionStageService->setAffectationStageService($serviceProvider);

        $serviceProvider->setSessionStageService($sessionStageService);


        return $serviceProvider;
    }
}
