<?php


namespace Application\View\Helper\SessionsStages\Factory;


use Application\Service\Preference\PreferenceService;
use Application\Service\Stage\SessionStageService;
use Application\View\Helper\SessionsStages\SessionStageViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class SessionStageViewHelperFactory
 * @package Application\View\Helper\SessionsStages
 */
class SessionStageViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SessionStageViewHelper
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SessionStageViewHelper
    {
        $vh = new SessionStageViewHelper();

        /** @var SessionStageService $sessionStageService */
        $sessionStageService = $container->get(ServiceManager::class)->get(SessionStageService::class);
        $vh->setSessionStageService($sessionStageService);

        /** @var PreferenceService $preferencesService */
        $preferencesService = $container->get(ServiceManager::class)->get(PreferenceService::class);
        $vh->setPreferenceService($preferencesService);

        return $vh;
    }

}