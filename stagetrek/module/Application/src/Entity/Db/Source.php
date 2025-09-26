<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

class Source implements ResourceInterface,
    HasCodeInterface, HasLibelleInterface, HasOrderInterface
{
    const RESOURCE_ID = 'Source';

    const STAGETREK = 'stagetrek';
    const APOGEE = 'apogee';
    const PEGASE = 'pegase';
    const CSV = 'csv';
    const LDAP = 'ldap';
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }


    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasOrderTrait;

    protected bool $importable = true;
    protected bool $lock = false;
    protected bool $synchro_insert_enabled = false;
    protected bool $synchro_update_enabled = false;
    protected bool $synchro_undelete_enabled = false;
    protected bool $synchro_delete_enabled = false;

    public function isImportable(): bool
    {
        return $this->importable;
    }

    public function setImportable(bool $importable): static
    {
        $this->importable = $importable;
        return $this;
    }

    public function isLock(): bool
    {
        return $this->lock;
    }

    public function setLock(bool $lock): static
    {
        $this->lock = $lock;
        return $this;
    }

    public function isSynchroInsertEnabled(): bool
    {
        return $this->synchro_insert_enabled;
    }

    public function setSynchroInsertEnabled(bool $synchro_insert_enabled): static
    {
        $this->synchro_insert_enabled = $synchro_insert_enabled;
        return $this;
    }

    public function isSynchroUpdateEnabled(): bool
    {
        return $this->synchro_update_enabled;
    }

    public function setSynchroUpdateEnabled(bool $synchro_update_enabled): static
    {
        $this->synchro_update_enabled = $synchro_update_enabled;
        return $this;
    }

    public function isSynchroUndeleteEnabled(): bool
    {
        return $this->synchro_undelete_enabled;
    }

    public function setSynchroUndeleteEnabled(bool $synchro_undelete_enabled): static
    {
        $this->synchro_undelete_enabled = $synchro_undelete_enabled;
        return $this;
    }

    public function isSynchroDeleteEnabled(): bool
    {
        return $this->synchro_delete_enabled;
    }

    public function setSynchroDeleteEnabled(bool $synchro_delete_enabled): static
    {
        $this->synchro_delete_enabled = $synchro_delete_enabled;
        return $this;
    }

}
