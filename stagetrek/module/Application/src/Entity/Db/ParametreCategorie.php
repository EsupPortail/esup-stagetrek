<?php

namespace Application\Entity\Db;


use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
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

    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
}
