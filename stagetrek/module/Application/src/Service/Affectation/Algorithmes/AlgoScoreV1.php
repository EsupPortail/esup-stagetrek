<?php

namespace Application\Service\Affectation\Algorithmes;

use Application\Entity\Db\SessionStage;
use Application\Exceptions\ProcedureAffectationException;
use Exception;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
class AlgoScoreV1 extends AbstractAlgorithmeAffectation
{

    /**
     * @throws ProcedureAffectationException
     */
    public function run(SessionStage $sessionStage) : static
    {
        $procedure= "algo_score_v1";
        $sessionId = $sessionStage->getId();
        try {
            $this->beginTransaction(); // suspend auto-commit
            $plsql = sprintf("call %s(%s);", $procedure, $sessionId);
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