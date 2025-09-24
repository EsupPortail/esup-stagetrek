<?php

namespace Application\Service\AnneeUniversitaire\Factory;

use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Application\Service\Groupe\GroupeService;
use Application\Service\Stage\SessionStageService;
use Application\Service\Stage\StageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenEtat\Service\EtatInstance\EtatInstanceService;
use UnicaenEtat\Service\EtatType\EtatTypeService;
use UnicaenTag\Service\Tag\TagService;

/**
 * Class AnneeUniversitaireServiceFactory
 * @package Application\Service\AnneeUniversitaire
 */
class AnneeUniversitaireServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AnneeUniversitaireService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnneeUniversitaireService
    {
        $serviceProvider = new AnneeUniversitaireService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);


        /** @var GroupeService $groupeService */
        $groupeService = $container->get(ServiceManager::class)->get(GroupeService::class);
        $serviceProvider->setGroupeService($groupeService);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $serviceProvider->setSessionStageService($sessionStageService);
        /** @var StageService $stageService */
        $stageService = $container->get(ServiceManager::class)->get(StageService::class);
        $serviceProvider->setStageService($stageService);

        $serviceProvider->setEtatTypeService($container->get(EtatTypeService::class));
        $serviceProvider->setEtatInstanceService($container->get(EtatInstanceService::class));

        $serviceProvider->setTagService($container->get(TagService::class));

        return $serviceProvider;
    }
}
