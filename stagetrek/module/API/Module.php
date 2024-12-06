<?php

namespace API;

use Laminas\Config\Factory as ConfigFactory;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $serviceManager->get('translator');
        $eventManager = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        /* Active un layout spécial si la requête est de type AJAX. Valable pour TOUS les modules de l'application. */
        $eventManager->getSharedManager()->attach('Laminas\Mvc\Controller\AbstractActionController', 'dispatch',
            function (MvcEvent $e) {
                $request = $e->getRequest();
                if ($request instanceof HttpRequest && $request->isXmlHttpRequest()) {
                    $e->getTarget()->layout('layout/ajax.phtml');
                }
            }
        );

        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
//        $serviceManager->get(SessionManager::class);
    }

    public function getConfig()
    {
        $configInit = [
            __DIR__ . '/config/module.config.php'
        ];
        $configFiles = ArrayUtils::merge(
            $configInit,
            Glob::glob(__DIR__ . '/config/merged/{,*.}{config}.php', Glob::GLOB_BRACE)
        );
        return ConfigFactory::fromFiles($configFiles);
    }

    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
