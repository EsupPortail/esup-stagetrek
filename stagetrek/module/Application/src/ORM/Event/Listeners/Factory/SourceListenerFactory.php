<?php

namespace Application\ORM\Event\Listeners\Factory;

use Application\Entity\Db\Source;
use Application\ORM\Event\Listeners\SourceListener;
use Psr\Container\ContainerInterface;

class SourceListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return SourceListener
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \UnicaenDbImport\Config\ConfigException
     */
    public function __invoke(ContainerInterface $container): SourceListener
    {

        $listener = new SourceListener();
        $listener->setSourceEntityClass(Source::class);
        $listener->setDefaultSourceCode(Source::STAGETREK);

        return $listener;
    }
}
