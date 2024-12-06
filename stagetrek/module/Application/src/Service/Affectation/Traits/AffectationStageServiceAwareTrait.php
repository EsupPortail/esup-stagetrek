<?php

namespace Application\Service\Affectation\Traits;

use Application\Service\Affectation\AffectationStageService;

/**
 * Traits AffectationStageServiceAwareTrait
 * @package Application\Service\AffectationStage
 */
trait AffectationStageServiceAwareTrait
{
    /** @var AffectationStageService|null $anneeUniversitaireService */
    protected ?AffectationStageService $affectationStageService = null;

    /**
     * @return AffectationStageService
     */
    public function getAffectationStageService() : AffectationStageService
    {
        return $this->affectationStageService;
    }

    /**
     * @param AffectationStageService $affectationStageService
     * @return AffectationStageServiceAwareTrait
     */
    public function setAffectationStageService(AffectationStageService $affectationStageService) : static
    {
        $this->affectationStageService = $affectationStageService;
        return $this;
    }
}