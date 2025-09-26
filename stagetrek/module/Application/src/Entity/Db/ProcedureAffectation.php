<?php

namespace Application\Entity\Db;

use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasDescriptionTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
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

    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasDescriptionTrait;
    use HasOrderTrait;
}
