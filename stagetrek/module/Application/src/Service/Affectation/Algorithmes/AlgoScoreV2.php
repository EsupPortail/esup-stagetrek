<?php

namespace Application\Service\Affectation\Algorithmes;

use Application\Entity\Db\SessionStage;
use Application\Exceptions\ProcedureAffectationException;
use Exception;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
class AlgoScoreV2 extends AbstractAlgorithmeAffectation
{

    /**
     * Concervé au cas ou
     * @throws ProcedureAffectationException
     */
    public function run(SessionStage $sessionStage) : static
    {
        $procedure= "algo_score_v2";
        try {
            $this->beginTransaction(); // suspend auto-commit
            $plsql = sprintf("call %s(%s);", $procedure, $sessionStage->getId());
            $stmt = $this->getObjectManager()->getConnection()->prepare($plsql);
            $stmt->execute();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw new ProcedureAffectationException($e->getMessage());
        }
        return $this;
    }



}