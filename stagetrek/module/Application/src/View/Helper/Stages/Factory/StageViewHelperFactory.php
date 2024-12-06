<?php


namespace Application\View\Helper\Stages\Factory;

use Application\View\Helper\Stages\StageViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class StageViewHelperFactory
 * @package Application\View\Helper\Stages
 */
class StageViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return StageViewHelper
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var StageViewHelper $vh */
        $vh = new StageViewHelper();
        return $vh;
    }

}