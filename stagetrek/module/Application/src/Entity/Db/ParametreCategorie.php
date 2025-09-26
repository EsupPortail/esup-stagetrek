<?php

namespace Application\Entity\Db;


use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * ParametreCategorie
 */
class ParametreCategorie implements ResourceInterface
{
    const RESOURCE_ID = 'ParametreCategorie';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    const DATE = 'date';
    const PREFERENCES = 'pref';
    const VALIDATION_STAGE = 'validation_stage';
    const CONVENTION_STAGE = 'convention';
    const PROCEDURE_AFFECTATION = 'procedure';
    const MAIL = 'mail';
    const FOOTER = 'footer';
    const LOG = 'log';

    use HasIdTrait;
    use HasLibelleTrait;
    use HasOrderTrait;
}
