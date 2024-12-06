<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\DescriptionEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Disponibilite
 */
class ProcedureAffectation implements ResourceInterface
{
    const RESOURCE_ID = 'ProcedureAffectation';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use CodeEntityTrait;
    use LibelleEntityTrait;
    use DescriptionEntityTrait;
    use OrderEntityTrait;
}
