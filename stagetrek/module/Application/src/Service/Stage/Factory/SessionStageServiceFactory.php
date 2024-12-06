<?php
namespace Application\Service\Stage\Factory;

use Application\Service\Affectation\AffectationStageService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Stage\SessionStageService;
use Application\Service\Stage\StageService;
use Application\Service\Stage\ValidationStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;

/**
 * Class SessionStageServiceFactory
 * @package Application\Service\SessionStage
 */
class SessionStageServiceFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SessionStageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageService
    {
        $serviceProvider = new SessionStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $serviceProvider->setStageService($stageService);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);

        /** @var AffectationStageService $affectationStageService */
        $affectationStageService = $container->get(ServiceManager::class)->get(AffectationStageService::class);
        $serviceProvider->setAffectationStageService($affectationStageService);


        /** @var ValidationStageService $validationService */
        $validationService = $container->get(ServiceManager::class)->get(ValidationStageService::class);
        $serviceProvider->setValidationStageService($validationService);


        $serviceProvider->setEtatTypeService($container->get(EtatTypeService::class));
        $serviceProvider->setEtatInstanceService($container->get(EtatInstanceService::class));

        return $serviceProvider;
    }
}