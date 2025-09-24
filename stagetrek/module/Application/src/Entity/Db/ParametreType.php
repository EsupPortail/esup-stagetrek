<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Interfaces\HasOrderInterface;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\InterfaceImplementation\HasOrderTrait;

/**
 * ParametreType
 */
class ParametreType
implements HasLibelleInterface, HasOrderInterface
{
    const RESOURCE_ID = 'ParametreType';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    const NO_TYPE = 'n/a';
    const STRING = 'string';
    const INT = 'int';
    const FLOAT = 'float';
    const BOOL = 'bool';

    use HasIdTrait;
    use HasLibelleTrait;
    use HasOrderTrait;


    /**
     * @var string|null
     */
    private ?string $castFonction = null;

    /**
     * Set castFonction.
     *
     * @param string|null $castFonction
     * @return \Application\Entity\Db\ParametreType
     */
    public function setCastFonction(?string $castFonction = null) : static
    {
        $this->castFonction = $castFonction;
        return $this;
    }

    /**
     * Get castFonction.
     *
     * @return string|null
     */
    public function getCastFonction(): ?string
    {
        return $this->castFonction;
    }

}
