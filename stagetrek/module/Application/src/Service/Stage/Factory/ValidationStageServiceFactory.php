<?php

namespace Application\Service\Stage\Factory;

use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Mail\MailService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Stage\StageService;
use Application\Service\Stage\ValidationStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;

/**
 * Class ValidationStageServiceFactory
 * @package Application\Service\Stage
 */
class ValidationStageServiceFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ValidationStageService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ValidationStageService
    {
        $serviceProvider = new ValidationStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        /** @var MailService $mailService */
        $mailService = $container->get(ServiceManager::class)->get(MailService::class);
        $serviceProvider->setMailService($mailService);

        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $serviceProvider->setStageService($stageService);

        /** @var EtudiantService $etudiantService */
        $etudiantService = $container->get(ServiceManager::class)->get(EtudiantService::class);
        $serviceProvider->setEtudiantService($etudiantService);

        /** @var ContrainteCursusEtudiantService $contrainteCursusEtudiantService */
        $contrainteCursusEtudiantService = $container->get(ServiceManager::class)->get(ContrainteCursusEtudiantService::class);
        $serviceProvider->setContrainteCursusEtudiantService($contrainteCursusEtudiantService);

        $etatTypeService = $container->get(EtatTypeService::class);
        $etatTypeInstanceService = $container->get(EtatInstanceService::class);
        $serviceProvider->setEtatTypeService($etatTypeService);
        $serviceProvider->setEtatInstanceService($etatTypeInstanceService);
        return $serviceProvider;
    }
}