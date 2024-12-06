<?php

namespace Application\Service\Parametre;


use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Service\Misc\CommonEntityService;

class ParametreCoutTerrainService extends CommonEntityService
{

    /** @return string */
    public function getEntityClass(): string
    {return ParametreTerrainCoutAffectationFixe::class;}

    public function add(mixed $entity, string $serviceEntityClass = null): ParametreTerrainCoutAffectationFixe
    {
        /** @var ParametreTerrainCoutAffectationFixe $parametre */
        $parametre = $entity;
        if($parametre->getUseCoutMedian()){$parametre->setCout(0);}
        $this->getObjectManager()->persist($parametre);
        $this->getObjectManager()->flush($parametre);
        return $parametre;
    }

    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        if($entity->getUseCoutMedian()){$entity->setCout(0);}
        $this->getObjectManager()->flush($entity);
        return$entity;
    }

    /**
     * @return $this
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        $this->getObjectManager()->remove($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
        }
        return $this;
    }
}