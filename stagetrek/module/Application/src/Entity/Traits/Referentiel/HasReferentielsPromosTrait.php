<?php

namespace Application\Entity\Traits\Referentiel;

use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\SessionStage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 */
trait HasReferentielsPromosTrait
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $referentielsPromos;

    /**
     * @param ReferentielPromo $referentielsPromos
     * @return \Application\Entity\Traits\Stage\HasSessionsStagesTrait
     */
    public function addReferentielPromo(ReferentielPromo $referentiel): static
    {
        if (!$this->referentielsPromos->contains($referentiel)) {
            $this->referentielsPromos->add($referentiel);
        }
        return $this;
    }

    /**
     * Remove groupe.
     *
     * @param SessionStage $referentiel
     * @return \Application\Entity\Traits\Stage\HasSessionsStagesTrait
     */
    public function removeReferentielPromo(ReferentielPromo $referentiel): static
    {
        $this->referentielsPromos->removeElement($referentiel);
        return $this;
    }

    /**
     * Get groupes
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReferentielsPromos(): Collection
    {
        return $this->referentielsPromos;
    }

    /**
     * @return void
     */
    protected function initReferentielsPromosCollection(): void
    {
        $this->referentielsPromos = new ArrayCollection();
    }
}