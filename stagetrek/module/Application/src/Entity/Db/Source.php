<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\CodeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

class Source implements ResourceInterface,
    CodeEntityInterface, LibelleEntityInterface, OrderEntityInterface
{
    const RESOURCE_ID = 'Source';
    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use CodeEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
}
