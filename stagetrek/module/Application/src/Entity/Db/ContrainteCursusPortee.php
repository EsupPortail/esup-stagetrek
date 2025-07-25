<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Mpdf\Tag\Li;

/**
 * ContrainteCursusPortee
 */
class ContrainteCursusPortee implements ResourceInterface,
    OrderEntityInterface, LibelleEntityInterface
{
    const RESOURCE_ID = 'ContrainteCursus';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    const GENERALE = 'general';
    const CATEGORIE = 'categorie';
    const TERRAIN = 'terrain';

    public function isType(string $code): bool
    {
        return $this->getCode() == $code;
    }

    use IdEntityTrait;
    use CodeEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
}
