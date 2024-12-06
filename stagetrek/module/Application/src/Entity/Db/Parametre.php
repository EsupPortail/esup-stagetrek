<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\CodeEntityInterface;
use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Interfaces\OrderEntityInterface;
use Application\Entity\Traits\InterfaceImplementation\CodeEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\DescriptionEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\OrderEntityTrait;
use Application\Entity\Traits\Parametre\HasParametreCategorieTrait;
use Application\Entity\Traits\Parametre\HasParametreTypeTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Parametre
 */
class Parametre implements ResourceInterface,
    LibelleEntityInterface, CodeEntityInterface, OrderEntityInterface
{
    const RESOURCE_ID = 'Parametre';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? uniqid();
        $prefixe = ($param['prefixe']) ?? 'parametre';
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 64);
    }


    public static function sortParametres(array $parametres): array
    {
        usort($parametres, function (Parametre $p1, Parametre $p2) {
            if ($p1->getParametreCategorie()->getOrdre() != $p2->getParametreCategorie()->getOrdre()) {
                return $p1->getParametreCategorie()->getOrdre() - $p2->getParametreCategorie()->getOrdre();
            }
            if ($p1->getOrdre() != $p2->getOrdre()) {
                return $p1->getOrdre() - $p2->getOrdre();
            }
            return $p1->getId() - $p2->getId();
        });
        return $parametres;
    }

    public function __toString()
    {
        return $this->getValue();
    }


    use IdEntityTrait;
    use LibelleEntityTrait;
    use OrderEntityTrait;
    use CodeEntityTrait;
    use DescriptionEntityTrait;

    use HasParametreTypeTrait;
    use HasParametreCategorieTrait;


    /**
     * @var string|null
     */
    protected ?string $value = null;

    /**
     * Get value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set value.
     *
     * @param string|null $value
     *
     * @return Parametre
     */
    public function setValue(?string $value): static
    {
        $this->value = $value;
        return $this;
    }


}
