<?php

namespace Application\Service\Referentiel\Traits;

use Application\Service\Referentiel\ReferentielService;

trait ReferentielServiceAwareTrait
{
    /** @var \Application\Service\Referentiel\ReferentielService|null */
    protected ?ReferentielService $referentielService = null;

    public function getReferentielService(): ReferentielService
    {
        return $this->referentielService;
    }

    public function setReferentielService(ReferentielService $referentielService): static
    {
        $this->referentielService = $referentielService;
        return $this;
    }

}