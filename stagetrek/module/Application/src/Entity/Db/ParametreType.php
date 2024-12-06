<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;

/**
 * ParametreType
 */
class ParametreType
implements LibelleEntityInterface, OrderEntityInterface
{
    const RESOURCE_ID = 'ParametreType';
    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }
    //TODO : ajouter un code, un provider, un champs isNumeric (pour que gérer le type d'input dans le formulaire

    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;

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
