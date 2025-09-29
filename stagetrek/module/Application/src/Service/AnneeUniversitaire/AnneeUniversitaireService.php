<?php

namespace Application\Service\AnneeUniversitaire;


use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Interfaces\LockableEntityInterface;
use Application\Provider\EtatType\AnneeEtatTypeProvider;
use Application\Provider\Tag\TagProvider;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Interfaces\LockableEntityServiceInterface;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use Application\Service\Misc\Traits\LockableEntityServiceTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use DateTime;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenTag\Service\Tag\TagServiceAwareTrait;

class AnneeUniversitaireService extends CommonEntityService
    implements LockableEntityServiceInterface
{
    use GroupeServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    use StageServiceAwareTrait;
    use TagServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return AnneeUniversitaire::class;
    }


//    Retourne l'année en cours si elle existe, sinon l'année futur la plus proche sinon l'année passé la plus proche

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported|\DateMalformedStringException
     */
    public function findAnneeCourante() : ?AnneeUniversitaire
    {
        $annees = $this->findAllBy([],['dateDebut' => 'desc', 'dateFin'=>'desc']);
        $enCours = array_filter($annees, function (AnneeUniversitaire $a){
            $today = new DateTime();
            return $a->getDateDebut() <= $today && $today < $a->getDateFin();
        });
        if(!empty($enCours)){
            return end($enCours);
        }
        return null;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return ?AnneeUniversitaire
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, string $serviceEntityClass = null): ?AnneeUniversitaire
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $entity;
        $this->getObjectManager()->persist($annee);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($annee);
            $this->recomputeOrdresAnnees();
            $this->updateEtat($annee);
            //Pas de maj des session, il n'y en as pas lors de l'ajout
        }
        return $annee;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return \Application\Entity\Db\AnneeUniversitaire|null
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): ?AnneeUniversitaire
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $entity;
        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $oldData = $unitOfwork->getOriginalEntityData($annee);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($annee);
            if((isset($oldData['dateDebut']) && $oldData['dateDebut'] != $annee->getDateDebut())
                || (isset($oldData['dateFin']) && $oldData['dateFin'] != $annee->getDateFin())
            ){
                $this->recomputeOrdresAnnees();
            }

        }
        return $annee;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return $this
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        //Sécurité
        if($entity->isLocked()){throw new Exception("L'année ne doit pas être vérouillée pour être supprimée.");}
        if(!empty($entity->getSessionsStages())){throw new Exception("L'année ne doit pas avoir de session pour être supprimée.");}
        if(!$entity->getGroupes()->isEmpty()){throw new Exception("L'année ne doit pas avoir de groupe pour être supprimée.");}

        $this->getObjectManager()->remove($entity);
        $this->getObjectManager()->flush($entity);
        $this->recomputeOrdresAnnees();
        return $this;
    }

    use LockableEntityServiceTrait {
        lock as protected _lock;
        unlock as protected _unlock;
    }

    /**
     * @param \Application\Entity\Db\AnneeUniversitaire $entity
     * @return \Application\Entity\Db\AnneeUniversitaire
     * @throws \Exception
     */
    public function lock(LockableEntityInterface $entity): AnneeUniversitaire
    {
        $this->_lock($entity);
        $this->getObjectManager()->flush();
        $this->triggerUpdateEtat($entity);
        return $entity;
    }

    /**
     * @param \Application\Entity\Db\AnneeUniversitaire $entity
     * @return \Application\Entity\Db\AnneeUniversitaire
     * @throws \Exception
     */
    public function unlock(LockableEntityInterface $entity): AnneeUniversitaire
    {
        $this->_unlock($entity);
        $this->getObjectManager()->flush();
        $this->triggerUpdateEtat($entity);
        return $entity;
    }

    /**
     * @return $this
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function recomputeOrdresAnnees() : static
    {
        $annees = $this->getObjectRepository()->findBy([], ['dateDebut' => 'asc', 'dateFin' => 'asc', 'id' => 'asc']);
        /** @var AnneeUniversitaire|null $previous */
        $previous = null;
        $hasChange = false;
        /** @var AnneeUniversitaire $a */
        foreach ($annees as  $a){
            if($a->getAnneePrecedente() !== $previous){
                $a->setAnneePrecedente($previous);
                $this->getObjectManager()->persist($a);
                $hasChange = true;
            }
            $previous = $a;
        }
        if(!$hasChange){//Pas de changement dans l'ordres inutile de faire d'autres changement
            return $this;
        }

        $annees = array_reverse($annees);
        $next = null;
        foreach ($annees as  $a){
            $a->setAnneeSuivante($next);
            $this->getObjectManager()->persist($a);
            $next = $a;
        }

        $this->getObjectManager()->flush();
//        Modifier l'ordre de l'année modiie également l'ordre des groupes
        $this->getGroupeService()->recomputeOrdresGroupes();
//        Pour les sessions c'est fait indirectement dans lors de la maj des groupes
//        $this->getSessionStageService()->recomputeOrdresSessions();

        return $this;
    }

    use EntityEtatServiceAwareTrait;

    /**
     * @desc : provoque la mise à jour de l'état de l'année, mais aussi des sessions et des stages en liens
     * @throws \Exception
     */
    protected function triggerUpdateEtat(AnneeUniversitaire $annee) : AnneeUniversitaire
    {
        $this->updateEtat($annee);
        $sessions = $annee->getSessionsStages();
        $this->getSessionStageService()->updateEtats($sessions);

        $sessions = $annee->getSessionsStages();
        $stages = [];
        foreach ($sessions as $session) {
            foreach ($session->getStages() as $stage) {
                $stages[] = $stage;
            }
        }
        $this->getStageService()->updateEtats($stages);
        $this->getSessionStageService()->updateEtats($sessions);
        return $annee;
    }

    protected function computeEtat(HasEtatsInterface $entity): string
    {
        if(!$entity instanceof AnneeUniversitaire){
            throw new Exception("L'entité fournise n'est pas une année universitaire.");
        }
        $annee = $entity;
        $today = new DateTime();
        if(!$annee->isLocked() &&  $annee->getDateDebut() < $today){
            $msg = sprintf("L'année universitaire n'est pas validée alors qu'elle est %s", ($today < $annee->getDateFin()) ? "en cours" : "terminée");
            $this->setEtatInfo($msg);
            return AnneeEtatTypeProvider::NON_VAlIDE;
        }

        return match (true) {
            !$annee->isLocked() => AnneeEtatTypeProvider::EN_CONSTRUCTION,
            $annee->getDateFin() < $today => AnneeEtatTypeProvider::TERMINE,
            $today < $annee->getDateDebut() => AnneeEtatTypeProvider::FURTUR,
            default => AnneeEtatTypeProvider::EN_COURS,
        };
    }

}