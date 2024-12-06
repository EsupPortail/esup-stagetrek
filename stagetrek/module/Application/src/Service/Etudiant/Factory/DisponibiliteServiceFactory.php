<?php

namespace Application\Service\Etudiant\Factory;


use Application\Service\Affectation\AffectationStageService;
use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Etudiant\DisponibiliteService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Stage\StageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class DisponibiliteServiceFactory
 * @package Application\Service\EntityService\Factory
 */
class DisponibiliteServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DisponibiliteService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DisponibiliteService
    {
        $serviceProvider = new DisponibiliteService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);


        /** @var \Application\Service\Etudiant\EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $serviceProvider->setStageService($stageService);

        /** @var AffectationStageService $affectationService */
        $affectationService = $container->get(ServiceManager::class)->get(AffectationStageService::class);
        $serviceProvider->setAffectationStageService($affectationService);


        /** @var ContrainteCursusEtudiantService $contrainteService */
        $contrainteService = $container->get(ServiceManager::class)->get(ContrainteCursusEtudiantService::class);
        $serviceProvider->setContrainteCursusEtudiantService($contrainteService);

        return $serviceProvider;
    }
}
