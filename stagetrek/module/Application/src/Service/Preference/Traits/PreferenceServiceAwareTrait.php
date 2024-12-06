<?php

namespace Application\Service\Preference\Traits;


use Application\Service\Preference\PreferenceService;

/**
 * Traits PreferenceServiceAwareTrait
 * @package Application\Service\Preference
 */
Trait PreferenceServiceAwareTrait
{
    /** @var PreferenceService|null $preferenceService */
    protected ?PreferenceService $preferenceService = null;

    /**
     * @return PreferenceService
     */
    public function getPreferenceService(): PreferenceService
    {
        return $this->preferenceService;
    }

    /**
     * @param PreferenceService $preferenceService
     * @return \Application\Service\Preference\Traits\PreferenceServiceAwareTrait
     */
    public function setPreferenceService(PreferenceService $preferenceService) : static
    {
        $this->preferenceService = $preferenceService;
        return $this;
    }

}