<?php

namespace Application\Service\Referentiel\Traits;

use Application\Service\Referentiel\ReferentielPromoService;

trait ReferentielPromoServiceAwareTrait
{
    /** @var \Application\Service\Referentiel\ReferentielPromoService|null */
    protected ?ReferentielPromoService $referentielPromoService = null;

    public function getReferentielPromoService(): ReferentielPromoService
    {
        return $this->referentielPromoService;
    }

    public function setReferentielPromoService(ReferentielPromoService $referentielPromoService): static
    {
        $this->referentielPromoService = $referentielPromoService;
        return $this;
    }

}