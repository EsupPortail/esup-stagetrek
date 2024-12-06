<?php

namespace UnicaenStorage;

use Laminas\ServiceManager\AbstractPluginManager;
use UnicaenStorage\Adapter\FilesystemStorageAdapter;
use UnicaenStorage\Adapter\FilesystemStorageAdapterFactory;
use UnicaenStorage\Adapter\S3StorageAdapter;
use UnicaenStorage\Adapter\S3StorageAdapterFactory;
use UnicaenStorage\Adapter\StorageAdapterInterface;

class StorageAdapterManager extends AbstractPluginManager
{
    protected $instanceOf = StorageAdapterInterface::class;

    protected $factories = [
        FilesystemStorageAdapter::class => FilesystemStorageAdapterFactory::class,
        S3StorageAdapter::class => S3StorageAdapterFactory::class,
    ];

    protected $aliases = [
        'FilesystemStorageAdapter' => FilesystemStorageAdapter::class,
        'S3StorageAdapter' => S3StorageAdapter::class,
    ];
}