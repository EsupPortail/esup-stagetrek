<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\Groupe\HasGroupesTrait;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;
use Application\Entity\Traits\Referentiel\HasSourceTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

class ReferentielPromo implements ResourceInterface,
    HasCodeInterface, HasLibelleInterface, HasOrderInterface
{
    const RESOURCE_ID = 'Promo';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initGroupesCollection();
    }
    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasOrderTrait;
    use HasSourceTrait;
    use HasGroupesTrait;

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
