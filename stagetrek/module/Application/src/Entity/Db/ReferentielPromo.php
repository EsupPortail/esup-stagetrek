<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\CodeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Application\Entity\Traits\Referentiel\HasSourceTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

class ReferentielPromo implements ResourceInterface,
    CodeEntityInterface, LibelleEntityInterface, OrderEntityInterface
{
    const RESOURCE_ID = 'Promo';

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
    use OrderEntityTrait;
    use HasSourceTrait;

    protected string $codePromo="";

    public function getCodePromo(): string
    {
        return $this->codePromo;
    }

    public function setCodePromo(string $codePromo): static
    {
        $this->codePromo = $codePromo;
        return $this;
    }
}
