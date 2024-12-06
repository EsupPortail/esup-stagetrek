<?php


namespace Application\View\Helper\Parametres\Factory;

use Application\View\Helper\Parametres\NiveauEtudeViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class NiveauEtudeViewHelperFactory
 */
class NiveauEtudeViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return NiveauEtudeViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): NiveauEtudeViewHelper
    {
        return new NiveauEtudeViewHelper();
    }

}