<?php


namespace Application\View\Helper\Affectation\Factory;

use Application\View\Helper\Affectation\AffectationViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AffectationViewHelperFactory
 * @package Application\View\Helper\Stages\Factory
 */
class AffectationViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AffectationViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AffectationViewHelper
    {
        return new AffectationViewHelper();
    }

}