<?php


namespace Application\View\Helper\Terrains\Factory;

use Application\View\Helper\Terrains\TerrainStageViewHelper;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class TerrainStageViewHelperFactory
 * @package AApplication\View\Helper\Terrains
 */
class TerrainStageViewHelperFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TerrainStageViewHelper
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TerrainStageViewHelper
    {
        return new TerrainStageViewHelper();
    }

}