<?php

namespace Application\Entity\Traits\Stage;

use Application\Entity\Db\ProcedureAffectation;

/**
 *
 */
Trait HasProcedureAffectationTrait
{
    /**
     * @var ProcedureAffectation|null
     */
    protected ?ProcedureAffectation $procedureAffectation = null;

    /**
     * @return \Application\Entity\Db\AffectationStage|null
     */
    public function getProcedureAffectation(): ?ProcedureAffectation
    {
        return $this->procedureAffectation;
    }

    /**
     * @param ProcedureAffectation|null $procedureAffectation
     * @return HasProcedureAffectationTrait
     */
    public function setProcedureAffectation(?ProcedureAffectation $procedureAffectation): static
    {
        $this->procedureAffectation = $procedureAffectation;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasProcedureAffectation(): bool
    {
        return $this->procedureAffectation !== null;
    }
}