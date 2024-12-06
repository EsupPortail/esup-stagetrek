<?php


namespace Application\View\Helper\Parametres\Factory;

use Application\Service\Parametre\ParametreService;
use Application\View\Helper\Parametres\ParametreViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class NiveauEtudeViewHelperFactory
 */
class ParametreViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ParametreViewHelper
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ParametreViewHelper
    {
        $vh = new ParametreViewHelper();

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $vh->setParametreService($parametreService);

        return $vh;
    }

}