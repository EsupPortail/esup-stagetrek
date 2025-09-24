<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Mpdf\Tag\Li;

/**
 * ContrainteCursusPortee
 */
class ContrainteCursusPortee implements ResourceInterface,
    HasOrderInterface, HasLibelleInterface
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

    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasOrderTrait;
}
