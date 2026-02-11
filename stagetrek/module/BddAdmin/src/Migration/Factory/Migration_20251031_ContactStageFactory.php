<?php

namespace BddAdmin\Migration\Factory;

use BddAdmin\Migration\Migration_20251031_ContactStage;
use Interop\Container\Containerinterface;

class Migration_20251031_ContactStageFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Migration_20251031_ContactStageFactory
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Migration_20251031_ContactStage
    {
        $script  = new Migration_20251031_ContactStage();

        $config = $container->get('Config');
        $version = ($config['unicaen-app']['app_infos']['version']) ?? null;
        $script->setVersion($version);
        return $script;
    }
}