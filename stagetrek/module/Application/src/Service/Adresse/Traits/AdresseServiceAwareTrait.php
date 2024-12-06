<?php

namespace Application\Service\Adresse\Traits;

use Application\Service\Adresse\AdresseService;

/**
 * Traits AdressesServicesAwareTrait
 */
trait AdresseServiceAwareTrait
{
    /** @var AdresseService|null $adresseService */
    protected ?AdresseService $adresseService = null;

    /**
     * @return \Application\Service\Adresse\AdresseService
     */
    public function getAdresseService(): AdresseService
    {
        return $this->adresseService;
    }

    /**
     * @param AdresseService|null $adresseService
     * @return $this
     */
    public function setAdresseService(?AdresseService $adresseService): static
    {
        $this->adresseService = $adresseService;
        return $this;
    }

}