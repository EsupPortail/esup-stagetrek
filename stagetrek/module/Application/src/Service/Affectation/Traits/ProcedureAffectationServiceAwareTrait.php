<?php

namespace Application\Service\Affectation\Traits;


use Application\Service\Affectation\ProcedureAffectationService;

/**
 * Traits PreferenceServiceAwareTrait
 * @package Application\Service\Preference
 */
Trait ProcedureAffectationServiceAwareTrait
{
    /** @var ProcedureAffectationService|null $procedureAffectationService */
    protected ?ProcedureAffectationService $procedureAffectationService = null;

    /**
     * @return ProcedureAffectationService
     */
    public function getProcedureAffectationService(): ProcedureAffectationService
    {
        return $this->procedureAffectationService;
    }

    /**
     * @param ProcedureAffectationService $procedureAffectationService
     * @return ProcedureAffectationServiceAwareTrait
     */
    public function setProcedureAffectationService(ProcedureAffectationService $procedureAffectationService) : static
    {
        $this->procedureAffectationService = $procedureAffectationService;
        return $this;
    }

}