<?php

namespace UnicaenStorage;

trait StorageAdapterManagerAwareTrait
{
    protected StorageAdapterManager $storageAdapterManager;

    /**
     * @param \UnicaenStorage\StorageAdapterManager $storageAdapterManager
     */
    public function setStorageAdapterManager(StorageAdapterManager $storageAdapterManager): void
    {
        $this->storageAdapterManager = $storageAdapterManager;
    }

}