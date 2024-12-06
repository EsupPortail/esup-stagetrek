<?php

namespace Application\Service\Affectation\Algorithmes;

use Application\Entity\Db\SessionStage;
use Application\Exceptions\ProcedureAffectationException;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Doctrine\DBAL\Result;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

/**
 * Class PreferenceService
 * @package Application\Service\Preference
 */
abstract class AbstractAlgorithmeAffectation implements
    ObjectManagerAwareInterface, AlgorithmeAffectationInterface
{
    use ProvidesObjectManager;
    use ParametreServiceAwareTrait;

    /**
     * @throws ProcedureAffectationException
     */
    public abstract function run(SessionStage $sessionStage) : static;


    /**
     * Proxy method.
     *
     * @see EntityManager::beginTransaction()
     */
    public function beginTransaction(): void
    {
        $this->getObjectManager()->beginTransaction();
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::commit()
     */
    public function commit(): void
    {
        $this->getObjectManager()->commit();
    }

    /**
     * Proxy method.
     *
     * @see EntityManager::rollback()
     */
    public function rollback(): void
    {
        $this->getObjectManager()->rollback();
    }

    /**
     * Exécute une requête
     *
     * @param string $sql
     * @param array $params
     * @return \Doctrine\DBAL\Result
     * @throws \Doctrine\DBAL\Exception
     */
    public function exec(string $sql, array $params = []) : Result
    {
        return $this->getObjectManager()->getConnection()->executeQuery($sql, $this->prepareParams($params));
    }


    /**
     * Préparation des paramètres à passer à la requête
     *
     * @param array $params
     * @return array
     */
    private function prepareParams(array $params = []): array
    {
        if (null == $params) $params = [];
        foreach ($params as $n => $v) {
            if (is_object($v) && method_exists($v, 'getId')) {
                $params[$n] = $v->getId();
            }
        }

        return $params;
    }

}