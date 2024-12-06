<?php

namespace Application\Service\ConventionStage\Factory;

use Application\Service\ConventionStage\ModeleConventionStageService;
use Application\Service\Renderer\MacroService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class ModeleConventionStageServiceFactory
 * @package Application\Service\Factory
 */
class ModeleConventionStageServiceFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModeleConventionStageService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModeleConventionStageService $serviceProvider */
        $serviceProvider = new ModeleConventionStageService();

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $serviceProvider->setObjectManager($entityManager);

        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $serviceProvider->setMacroService($macroService);

        return $serviceProvider;
    }
}