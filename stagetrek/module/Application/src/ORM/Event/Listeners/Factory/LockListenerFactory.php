<?php

namespace Application\ORM\Event\Listeners\Factory;

use Application\Entity\Db\Source;
use Application\ORM\Event\Listeners\LockListener;
use Application\ORM\Event\Listeners\SourceListener;
use Psr\Container\ContainerInterface;

class LockListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return LockListener
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \UnicaenDbImport\Config\ConfigException
     */
    public function __invoke(ContainerInterface $container): LockListener
    {

        $listener = new LockListener();
        return $listener;
    }
}
