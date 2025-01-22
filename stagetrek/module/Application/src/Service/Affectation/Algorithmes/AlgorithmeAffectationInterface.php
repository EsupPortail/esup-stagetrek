<?php

namespace Application\Service\Affectation\Algorithmes;

use Application\Entity\Db\SessionStage;
use Application\Exceptions\ProcedureAffectationException;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
Interface AlgorithmeAffectationInterface
{
    /**
     * @throws ProcedureAffectationException
     */
    public function run(SessionStage $sessionStage) : static;

    public static function getCodeAlgo() : string;
}