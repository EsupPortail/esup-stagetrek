<?php

namespace Application\Service\Preference\Factory;

use Application\Service\Affectation\AffectationStageService;
use Application\Service\Preference\PreferenceService;
use Application\Service\Stage\SessionStageService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class PreferenceServiceFactory
 * @package Application\Service\Preference
 */
class PreferenceServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PreferenceService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : PreferenceService
    {
        $serviceProvider = new PreferenceService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $serviceProvider->setSessionStageService($sessionStageService);

        /** @var AffectationStageService $affectationStageService */
        $affectationStageService = $container->get(ServiceManager::class)->get(AffectationStageService::class);
        $serviceProvider->setAffectationStageService($affectationStageService);

        return $serviceProvider;
    }
}
