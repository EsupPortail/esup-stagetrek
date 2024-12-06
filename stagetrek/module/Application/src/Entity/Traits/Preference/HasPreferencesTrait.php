<?php

namespace Application\Entity\Traits\Preference;

use Application\Entity\Db\Preference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasPreferencesTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $preferences;

    /**
     * @return void
     */
    protected function initPreferencesCollection(): void
    {
        $this->preferences = new ArrayCollection();
    }

    /**
     * @param Preference $preference
     * @return \Application\Entity\Traits\HasPreferencesTrait
     */
    public function addPreference(Preference $preference) : static
    {
        if(!$this->preferences->contains($preference)){
            $this->preferences->add($preference);
        }
        return $this;
    }

    /**
     * Remove preference.
     *
     * @param Preference $preference
     * @return \Application\Entity\Traits\HasPreferencesTrait
     */
    public function removePreference(Preference $preference) : static
    {
        $this->preferences->removeElement($preference);
        return $this;
    }

    /**
     * Get preferences
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreferences() : Collection
    {
        return $this->preferences;
    }

}