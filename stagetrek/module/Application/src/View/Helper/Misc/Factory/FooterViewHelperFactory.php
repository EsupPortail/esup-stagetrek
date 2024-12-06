<?php

namespace Application\View\Helper\Misc\Factory;

use Application\Service\Parametre\ParametreService;
use Application\View\Helper\Misc\FooterViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Class AlertFlashViewHelperFactory
 * @package Application\View\Helper\Messages\Factory
 */
class FooterViewHelperFactory implements FactoryInterface
{
    /**
     * Create view helper
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Application\View\Helper\Misc\FooterViewHelper
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : FooterViewHelper
    {
        $vh = new FooterViewHelper();

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $vh->setParametreService($parametreService);

        return $vh;
    }
}