<?php

namespace Application\Service\Misc\Factory;

use Application\Service\Misc\Entity\ModuleOptions;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ModuleOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Configuration');
        return new ModuleOptions(isset($config['StageTrek']) ? $config['StageTrek'] : []);
    }
}