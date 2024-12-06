<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
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

    //TODO : remplacer par des codes dans un provider
    const ID_PORTEE_GENERAL = 1;
    const ID_PORTEE_CATEGORIE = 2;
    const ID_PORTEE_TERRAIN = 3;

    public function isType(int $typeId): bool
    {
        return $this->getId() === $typeId;
    }

    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
}
