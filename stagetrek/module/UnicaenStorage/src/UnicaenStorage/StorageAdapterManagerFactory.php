<?php

namespace UnicaenStorage;

use Psr\Container\ContainerInterface;

class StorageAdapterManagerFactory
{
    public function __invoke(ContainerInterface $container): StorageAdapterManager
    {
        return new StorageAdapterManager($container);
    }
}