<?php

namespace Application\Service\Parametre;


use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Entity\Db\TerrainStage;
use Application\Service\Misc\CommonEntityService;

class ParametreCoutAffectationService extends CommonEntityService
{

    /** @return string */
    public function getEntityClass(): string
    {return ParametreCoutAffectation::class;}

    /** @return int
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function getNextRang(): int
    { return sizeof($this->findAll())+1;}
    public function findAll(): array
    {
        return $this->findAllBy([],["rang" => "ASC"]);
    }


    public function add($entity, $serviceEntityClass = null): mixed
    {
        $this->getObjectManager()->persist($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
        }
        return$entity;
    }

    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($entity);
        }
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
        $params = $this->findAll();
        $rang = 1;
        /** @var ParametreCoutAffectation $param */
        foreach ($params as $param){
            $param->setRang($rang++);
            $this->getObjectManager()->flush($param);
        }
        return $this;
    }

    //Gestion des terrains ayant un cout d'affectation fixe mis ici car parametres fortement liée

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAllParametresTerrainsCoutsFixes(): array
    {
        return $this->getObjectManager()->getRepository(ParametreTerrainCoutAffectationFixe::class)->findAll();
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findParametreTerrainCoutFixe($id){
        return $this->getObjectManager()->getRepository(ParametreTerrainCoutAffectationFixe::class)->find($id);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findParametreTerrainCoutFixeForTerrain(TerrainStage $terrain){
        return $this->getObjectManager()->getRepository(ParametreTerrainCoutAffectationFixe::class)->findOneBy(
            ['terrainStage' => $terrain->getId()]
        );
    }
}