<?php

namespace Application\View\Helper\Misc\Factory;

use Application\View\Helper\Misc\AlertFlashViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger as FlashMessengerPlugin;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class AlertFlashViewHelperFactory
 * @package Application\View\Helper\Messages\Factory
 */
class AlertFlashViewHelperFactory implements FactoryInterface
{
    /**
     * Create view helper
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AlertFlashViewHelper|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var FlashMessengerPlugin $flashMessengerPlugin
         * @var EventManagerInterface $eventManager
         */
        $flashMessengerPlugin = $container->get(PluginManager::class)->get('flashMessenger');
        $eventManager = $container->get('EventManager');

        $vh = new AlertFlashViewHelper();
        $vh->setPluginFlashMessenger($flashMessengerPlugin);
        $vh->setEventManager($eventManager);

        return $vh;
    }
}