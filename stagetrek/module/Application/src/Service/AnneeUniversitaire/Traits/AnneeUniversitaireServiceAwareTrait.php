<?php

namespace Application\Service\AnneeUniversitaire\Traits;


use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;

/**
 * Traits AnneeUniversitaireServiceAwareTrait
 * @package Application\Service\AnneeUniversitaire
 */
Trait AnneeUniversitaireServiceAwareTrait
{
    /** @var AnneeUniversitaireService|null $anneeUniversitaireService */
    protected ?AnneeUniversitaireService $anneeUniversitaireService = null;

    /**
     * @return AnneeUniversitaireService
     */
    public function getAnneeUniversitaireService(): AnneeUniversitaireService
    {
        return $this->anneeUniversitaireService;
    }

    /**
     * @param AnneeUniversitaireService $anneeUniversitaireService
     * @return \Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait
     */
    public function setAnneeUniversitaireService(AnneeUniversitaireService $anneeUniversitaireService): static
    {
        $this->anneeUniversitaireService = $anneeUniversitaireService;
        return $this;
    }
}