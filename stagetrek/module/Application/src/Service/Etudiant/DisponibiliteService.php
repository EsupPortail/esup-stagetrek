<?php

namespace Application\Service\Etudiant;

use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Stage;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Stage\Traits\StageServiceAwareTrait;

class DisponibiliteService extends CommonEntityService
{
    use EtudiantServiceAwareTrait;
    use StageServiceAwareTrait;
    use AffectationStageServiceAwareTrait;
    use ContrainteCursusEtudiantServiceAwareTrait;

    /** @return string */
    public function getEntityClass(): string
    {
        return Disponibilite::class;
    }

    /**
     * @param mixed $entity
     * @param null $serviceEntityClass
     * @return mixed
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, $serviceEntityClass = null): Disponibilite
    {
        /** @var Disponibilite $dispo */
        $dispo = $entity;
//        $this->retirerEtudiantSessions($dispo);
        $this->getObjectManager()->persist($dispo);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($dispo);
            $etudiant = $dispo->getEtudiant();
            $stages = $etudiant->getStages()->toArray();
            $affections = [];
            /** @var Stage $stage */
            foreach ($stages as $stage) {
                 $a = $stage->getAffectationStage();
                 if(isset($a)){
                     $affections[$a->getId()] = $a;
                 }
            }
            $this->getAffectationStageService()->updateEtats($affections);
            $this->getStageService()->updateEtats($stages);
            $contraintesCursusEtudiant = $etudiant->getContraintesCursusEtudiants()->toArray();
            //Potentiellement annule une validation
            $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiant);
            $this->getEtudiantService()->updateEtat($etudiant);
        }
        return $dispo;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var Disponibilite $dispo */
        $dispo = $entity;

        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $unitOfwork->computeChangeSets();
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($dispo);
            $etudiant = $dispo->getEtudiant();
            $stages = $etudiant->getStages()->toArray();
            $affections = [];
            /** @var Stage $stage */
            foreach ($stages as $stage) {
                $a = $stage->getAffectationStage();
                if(isset($a)){
                    $affections[$a->getId()] = $a;
                }
            }
            $this->getAffectationStageService()->updateEtats($affections);
            $this->getStageService()->updateEtats($stages);
            $contraintesCursusEtudiant = $etudiant->getContraintesCursusEtudiants()->toArray();
            //Potentiellement annule une validation
            $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiant);
            $this->getEtudiantService()->updateEtat($etudiant);
        }

        return $dispo;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        /** @var Disponibilite $dispo */
        $dispo = $entity;
        $etudiant = $dispo->getEtudiant();
        $this->getObjectManager()->remove($dispo);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($dispo);
            $stages = $etudiant->getStages()->toArray();
            $affections = [];
            /** @var Stage $stage */
            foreach ($stages as $stage) {
                $a = $stage->getAffectationStage();
                if(isset($a)){
                    $affections[$a->getId()] = $a;
                }
            }
            $this->getAffectationStageService()->updateEtats($affections);
            $this->getStageService()->updateEtats($stages);
            $contraintesCursusEtudiant = $etudiant->getContraintesCursusEtudiants()->toArray();
            //Potentiellement annule une validation
            $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiant);
            $this->getEtudiantService()->updateEtat($etudiant);
        }
        return $this;
    }
}