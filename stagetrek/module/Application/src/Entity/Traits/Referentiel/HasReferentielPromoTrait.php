<?php

namespace Application\Entity\Traits\Referentiel;

use Application\Entity\Db\ReferentielPromo;

/**
 *
 */
trait HasReferentielPromoTrait
{

    /**
     * @var \Application\Entity\Db\ReferentielPromo|null
     */
    protected ?ReferentielPromo $referentielPromo = null;

    /**
     * @return \Application\Entity\Db\ReferentielPromo|null
     */
    public function getReferentielPromo(): ?ReferentielPromo
    {
        return $this->referentielPromo;
    }

    /**
     * @param \Application\Entity\Db\ReferentielPromo|null $referentielPromo
     * @return \Referentiel\Entity\Db\Traits\HasReferentielPromoTrait
     */
    public function setReferentielPromo(?ReferentielPromo $referentielPromo): static
    {
        $this->referentielPromo = $referentielPromo;
        return $this;
    }

    public function hasReferentielPromo(): bool
    {
        return $this->referentielPromo !== null;
    }
}