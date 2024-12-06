<?php

namespace Application\Entity\Traits\Adresse;

use Application\Entity\Db\Adresse;

/**
 *
 */
trait HasAdresseTrait
{
    /** @var Adresse|null $adresse */
    protected ?Adresse $adresse = null;

    /**
     * @return \Application\Entity\Db\Adresse|null
     */
    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    /**
     * @param \Application\Entity\Db\Adresse|null $adresse
     * @return \Application\Entity\Traits\HasAdresseTrait
     */
    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAdresse(): bool
    {
        return $this->adresse !== null;
    }
}