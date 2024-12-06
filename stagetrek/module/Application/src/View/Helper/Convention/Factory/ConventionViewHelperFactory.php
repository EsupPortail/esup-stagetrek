<?php


namespace Application\View\Helper\Convention\Factory;

use Application\Service\ConventionStage\ConventionStageService;
use Application\View\Helper\Convention\ConventionViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

class ConventionViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ConventionViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $vh = new ConventionViewHelper();
        /** @var ConventionStageService $service */
        $service = $container->get(ServiceManager::class)->get(ConventionStageService::class);
        $vh->setConventionStageService($service);

        return $vh;
    }

}