<?php

namespace Application\Service\Contrainte;

use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\ContrainteCursusPortee;
use Application\Entity\Db\Etudiant;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;

/**
 * Class ContrainteCursusService
 * @package Application\Service\ContraintesService
 */
class ContrainteCursusService extends CommonEntityService
{
    use EtudiantServiceAwareTrait;
    use ContrainteCursusEtudiantServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return ContrainteCursus::class;
    }

    public function findAll(): array
    {
        $contraintes = parent::findAll();
        $contraintes = ContrainteCursus::sort($contraintes);
        return $contraintes;
    }

    /**
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return mixed
     * @throws \Exception
     */
    public function add(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var ContrainteCursus $contrainte */
        $contrainte = $entity;
        $this->getObjectManager()->persist($contrainte);
        if($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->recomputeEtudiantContrainteCursus();
            $this->getObjectManager()->refresh($contrainte);
            //Si la contrainte n'est pas contradictoire, elle n'impacte que les étudiants touché par cette contraintes et la contrainte en question
            if(!$contrainte->isContradictoire()){
                $contraintesCursusEtudiants = $this->getObjectManager()->getRepository(ContrainteCursusEtudiant::class)->findBy(['contrainteCursus' => $contrainte]);
                $etudiants = [];
                foreach ($contraintesCursusEtudiants as $ce){
                    $etudiants[] = $ce->getEtudiant();
                }
            }
            else{ //Contradiction = impactes toutes les également en contradiction
                $contraintesCursusEtudiants = $this->getObjectManager()->getRepository(ContrainteCursusEtudiant::class)->findAll();
                $etudiants = $this->getObjectManager()->getRepository(Etudiant::class)->findAll();
            }
            $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiants);
            $this->getEtudiantService()->updateEtats($etudiants);
        }
        return $entity;
    }

    /**
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return mixed
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var ContrainteCursus $contrainte */
        $contrainte = $entity;
        $wasContradictoire=$contrainte->isContradictoire();
        if($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($contrainte);
            $this->recomputeEtudiantContrainteCursus();

            $this->getObjectManager()->refresh($contrainte);
            //Si la contrainte n'est pas contradictoire, elle n'impacte que les étudiants touché par cette contraintes et la contrainte en question
            if(!$wasContradictoire && !$contrainte->isContradictoire()){
                $contraintesCursusEtudiants = $this->getObjectManager()->getRepository(ContrainteCursusEtudiant::class)->findBy(['contrainteCursus' => $contrainte]);
            }
            else{ //Contradiction = impactes toutes les contraintes en contradiction
                $contraintesCursusEtudiants = $this->getObjectManager()->getRepository(ContrainteCursusEtudiant::class)->findAll();
            }
            $etudiants = [];
            foreach ($contraintesCursusEtudiants as $ce){
                $etudiants[] = $ce->getEtudiant();
            }

            $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiants);
            $this->getEtudiantService()->updateEtats($etudiants);
        }
        return $entity;
    }

    /**
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return $this
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        /** @var ContrainteCursus $contrainte */
        $contrainte = $entity;
        $wasContradictoire = $contrainte->isContradictoire();
        $this->getObjectManager()->remove($entity);

        if($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->recomputeEtudiantContrainteCursus();
            //Du fait de la suppression, on ne doit recalculer les états que si elle était en contradiction
            if($wasContradictoire){
                $contraintesCursusEtudiants = $this->getObjectManager()->getRepository(ContrainteCursusEtudiant::class)->findAll();
                $etudiants = $this->getObjectManager()->getRepository(Etudiant::class)->findAll();
                $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiants);
                $this->getEtudiantService()->updateEtats($etudiants);
            }
        }
        return $this;
    }

    //Creer les contraintes cursus manquantes
    //Supprime les anciennes
    // Calcul les contradictions
    /**
     * @throws \Exception
     */
    protected function recomputeEtudiantContrainteCursus(): void
    {
        $this->execProcedure('update_contraintes_cursus');
    }

    public function findContraintesForTerrain(?\Application\Entity\Db\TerrainStage $terrain) : array
    {
        //TODO : revoir pourquoi on ne fait pas plutot une table ContrainteCursusTerrainsLinker
        //Recherche des contraintes de cursus sur le terrain de stage
        $c1 = $this->getObjectManager()->getRepository(ContrainteCursusPortee::class)->findBy(['code' => ContrainteCursusPortee::GENERALE]);
        $c2 = $this->getObjectManager()->getRepository(ContrainteCursusPortee::class)->findBy(['code' => ContrainteCursusPortee::CATEGORIE]);
        $c3 = $this->getObjectManager()->getRepository(ContrainteCursusPortee::class)->findBy(['code' => ContrainteCursusPortee::TERRAIN]);

        $contraintes1 = $this->findAllBy(['contrainteCursusPortee' => $c1], ['dateFin' => 'desc','dateDebut' => 'desc',]);
        $contraintes2 = $this->findAllBy(['contrainteCursusPortee' => $c2, 'categorieStage' => $terrain->getCategorieStage()], ['dateFin' => 'desc','dateDebut' => 'desc',]);
        $contraintes3 = $this->findAllBy(['contrainteCursusPortee' => $c3, 'terrainStage' => $terrain], ['dateFin' => 'desc','dateDebut' => 'desc',]);
        $contraintes2 = array_merge($contraintes1, $contraintes2);
        return array_merge($contraintes2, $contraintes3);
    }

}
