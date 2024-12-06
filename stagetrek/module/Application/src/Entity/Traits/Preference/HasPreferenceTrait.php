<?php

namespace Application\Entity\Traits\Preference;

use Application\Entity\Db\Preference;

/**
 *
 */
trait HasPreferenceTrait
{
    /**
     * @var \Application\Entity\Db\Preference|null
     */
    protected ?Preference $preference = null;

    /**
     * @return \Application\Entity\Db\Preference|null
     */
    public function getPreference(): ?Preference
    {
        return $this->preference;
    }

    /**
     * @param \Application\Entity\Db\Preference|null $preference
     * @return \Application\Entity\Traits\HasPreferenceTrait
     */
    public function setPreference(?Preference $preference): static
    {
        $this->preference = $preference;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasPreference(): bool
    {
        return $this->preference !== null;
    }

}