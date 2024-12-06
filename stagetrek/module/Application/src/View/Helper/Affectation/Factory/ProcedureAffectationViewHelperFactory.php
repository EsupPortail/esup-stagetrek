<?php


namespace Application\View\Helper\Affectation\Factory;

use Application\View\Helper\Affectation\ProcedureAffectationViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AffectationViewHelperFactory
 * @package Application\View\Helper\Stages\Factory
 */
class ProcedureAffectationViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ProcedureAffectationViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ProcedureAffectationViewHelper
    {
        return new ProcedureAffectationViewHelper();
    }

}