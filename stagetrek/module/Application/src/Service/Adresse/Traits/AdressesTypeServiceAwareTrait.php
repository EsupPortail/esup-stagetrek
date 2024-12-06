<?php

namespace Application\Service\Adresse\Traits;

use Application\Service\Adresse\AdresseTypeService;

trait AdressesTypeServiceAwareTrait
{
    /** @var AdresseTypeService|null $adresseTypeService */
    protected ?AdresseTypeService $adresseTypeService = null;

    /**
     * @return \Application\Service\Adresse\AdresseTypeService
     */
    public function getAdresseTypeService(): AdresseTypeService
    {
        return $this->adresseTypeService;
    }

    /**
     * @param \Application\Service\Adresse\AdresseTypeService|null $adresseTypeService
     * @return $this
     */
    public function setAdresseTypeService(?AdresseTypeService $adresseTypeService): static
    {
        $this->adresseTypeService = $adresseTypeService;
        return $this;
    }

}