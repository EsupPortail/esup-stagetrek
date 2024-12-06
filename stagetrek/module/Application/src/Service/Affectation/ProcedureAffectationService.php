<?php

namespace Application\Service\Affectation;

use Application\Entity\Db\ProcedureAffectation;
use Application\Entity\Db\SessionStage;
use Application\Exceptions\ProcedureAffectationException;
use Application\Provider\Parametre\ParametreProvider;
use Application\Service\Affectation\Algorithmes\AlgorithmeAffectationInterface;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
class ProcedureAffectationService extends CommonEntityService
{
    use AffectationStageServiceAwareTrait;
    use ParametreServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return ProcedureAffectation::class;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByCode(string $code): ?ProcedureAffectation
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function getProcedureCourante(): ?ProcedureAffectation
    {
        $code = $this->getParametreService()->getParametreValue(ParametreProvider::PROCEDURE_AFFECTATION);
        if(!isset($code)){return null;}
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * @throws \Application\Exceptions\ProcedureAffectationException
     * @throws \Exception
     */
    public function run(ProcedureAffectation $procedure, SessionStage $sessionStage) : static
    {
        $algo = $this->getAlgorithmeFor($procedure);
        if(!isset($algo)){
            $msg = sprintf("L'algorithme d'affectation correspondant a la procédure %s n'as pas été correctement configuré", $procedure->getCode());
            throw new ProcedureAffectationException($msg);
        }
        $algo->run($sessionStage);

        //Pour être sur de prendre en compte les modification
        $this->objectManager->refresh($sessionStage);
//        Misse à jours des états et des préférences satisfaites
        $affectations = $sessionStage->getAffectations();
        foreach ($affectations as $affectation) {
            $this->getAffectationStageService()->updatePreferenceSatisfaction($affectation);
            $this->getAffectationStageService()->updateEtat($affectation);
        }
        return $this;
    }



    protected array $algorithmes = [];
    public function getAlgorithmes() :array{
        return $this->algorithmes;
    }

    public function setAlgorithmes(array $algorithmes) : static
    {
        $this->algorithmes = $algorithmes;
        return $this;
    }

    public function getAlgorithmeFor(ProcedureAffectation $procedure) : ?AlgorithmeAffectationInterface
    {
        return ($this->algorithmes[$procedure->getCode()] ?? null);
    }
    public function setAlgorithmeFor(ProcedureAffectation $procedure, AlgorithmeAffectationInterface $algo) :static
    {
        $this->algorithmes[$procedure->getCode()]=$algo;
        return $this;
    }

}