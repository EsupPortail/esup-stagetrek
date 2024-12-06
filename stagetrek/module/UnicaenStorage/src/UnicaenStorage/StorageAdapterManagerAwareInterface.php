<?php

namespace UnicaenStorage;

interface StorageAdapterManagerAwareInterface
{
    public function setStorageAdapterManager(StorageAdapterManager $storageAdapterManager): void;
}