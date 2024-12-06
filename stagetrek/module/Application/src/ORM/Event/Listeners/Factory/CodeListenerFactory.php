<?php

namespace Application\ORM\Event\Listeners\Factory;

use Application\ORM\Event\Listeners\CodeListener;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CodeListenerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, '?');
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $listener = new CodeListener();
        return $listener;
    }
}
