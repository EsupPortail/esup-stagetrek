<?php

namespace Application\Entity\Traits\Adresse;

use Application\Entity\Db\AdresseType;

/**
 *
 */
Trait HasAdresseTypeTrait
{
    /**
     * @var \Application\Entity\Db\AdresseType|null
     */
    protected ?AdresseType $adresseType = null;

    /**
     * @param \Application\Entity\Db\AdresseType|null $adresseType
     * @return \Application\Entity\Traits\HasAdresseTypeTrait
     */
    public function setAdresseType(AdresseType $adresseType = null) :static
    {
        $this->adresseType = $adresseType;
        return $this;
    }

    /**
     * @return \Application\Entity\Db\AdresseType|null
     */
    public function getAdresseType(): ?AdresseType
    {
        return $this->adresseType;
    }

    /**
     * @return bool
     */
    public function hasAdresseType(): bool
    {
        return $this->adresseType !== null;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function isType(string $code): bool
    {
        return isset($this->adresseType) && $this->adresseType->hasCode($code);
    }
}