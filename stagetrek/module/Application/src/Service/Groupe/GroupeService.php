<?php

namespace Application\Service\Groupe;


use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Application\Form\Groupe\GroupeRechercheForm as FormRecherche;

/**
 * Class GroupeService
 * @package Application\Service\Groupe
 */
class GroupeService extends CommonEntityService
{
    use StageServiceAwareTrait;
    use EtudiantServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return Groupe::class;
    }

    public function findAll(): array
    {
        return $this->findAllBy([], ["libelle" => "ASC"]);
    }

    /**
     * @param array $criteria
     * @return Groupe[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function search(array $criteria): array
    {
        $qb = $this->getObjectRepository()->createQueryBuilder($alias = 'g');
        if (isset($criteria[FormRecherche::INPUT_NIVEAU])) {
            $qb->leftJoin("$alias.niveauEtude", 'n');
        }
        if (isset($criteria[FormRecherche::INPUT_ANNEE]) || isset($criteria[FormRecherche::INPUT_ETAT])) {
            $qb->leftJoin("$alias.anneeUniversitaire", 'a');
        }
        if (!empty($criteria[FormRecherche::INPUT_LIBELLE])) {
            $qb->andWhere($qb->expr()->like($qb->expr()->upper("$alias.libelle"), $qb->expr()->upper(':libelle')));
            $qb->setParameter('libelle', "{$criteria[FormRecherche::INPUT_LIBELLE]}%");
        }
        if (isset($criteria[FormRecherche::INPUT_NIVEAU])) {
            $qb->andWhere($qb->expr()->in("n.id", ":niveau"));
            $qb->setParameter('niveau', $criteria[FormRecherche::INPUT_NIVEAU]);
        }
        if (isset($criteria[FormRecherche::INPUT_ANNEE])) {
            $qb->andWhere($qb->expr()->in("a.id", ":annee"));
            $qb->setParameter('annee', $criteria[FormRecherche::INPUT_ANNEE]);
        }
        if (isset($criteria[FormRecherche::INPUT_ETAT])) {
//            On passe pas par le décorateur car l'état est celui de l'année, pas du groupe
            $qb->leftJoin("a.etats", 'etat');
            $qb->leftJoin("etat.type", 'type');
            $qb->andWhere('etat.histoDestruction IS NULL');
            $qb->andWhere($qb->expr()->in("type.id", ":etatType"));
            $qb->setParameter('etatType', $criteria[FormRecherche::INPUT_ETAT]);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @param mixed $entity
     * @param null $serviceEntityClass
     * @return mixed
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(mixed $entity, $serviceEntityClass = null): mixed
    {
        $this->getObjectManager()->persist($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->recomputeOrdresGroupes();
        }
        return $entity;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $oldData = $unitOfwork->getOriginalEntityData($entity);
        $this->getObjectManager()->persist($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            //On ne fait une maj que si le niveau d'étude change car c'est le seul changement qui impacte tout
            //Mais il s'agit d'un changement plus que rare donc inutile des test inutiles
            if(isset($oldData['niveau_etude_id']) &&
                $entity->getNiveauEtude()->getId() != $oldData['niveau_etude_id'])
            {
                $this->recomputeOrdresGroupes();
            }
        }
        return $entity;
    }

    /**
     * @param Groupe $groupe
     * @param Etudiant[] $etudiants
     * @return mixed
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function addEtudiants(Groupe $groupe, array $etudiants): Groupe
    {
        foreach ($etudiants as $etudiant) {
            $groupe->addEtudiant($etudiant);
        }
//        //ajout automatiques des liens entre la sessions et les étudiants du groupes si la date de début du stage n'est pas dépassé
        $today = new DateTime();
        /** @var SessionStage $session */
        foreach ($groupe->getSessionsStages() as $session) {
            if ($today < $session->getDateDebutStage() && ! $session->isSessionRattrapge()) {
                foreach ($etudiants as $etudiant) {
                    $stage = $this->getStageService()->createStage($etudiant, $session);
                    $this->getObjectManager()->persist($stage);
                    $this->getObjectManager()->persist($stage->getValidationStage());
                    $this->getObjectManager()->persist($stage->getAffectationStage());
                    $this->getStageService()->updateEtat($stage);
                }
            }
        }
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
        }
        foreach ($etudiants as $etudiant) {
            $this->getStageService()->recomputeOrdresStage(null, $etudiant);
        }
        $this->getEtudiantService()->updateEtats($etudiants);
        return $groupe;
    }

    /**
     * @param Groupe $groupe
     * @param Etudiant[] $etudiants
     * @return mixed
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function removeEtudiants(Groupe $groupe, array $etudiants): Groupe
    {
        foreach ($etudiants as $etudiant) {
            // Suppression du liens entre les sessions et les étudiants
            // Suppression des stages (et donc implicitement de tout ce qui va avec)
            foreach ($groupe->getSessionsStages() as $session) {
                $stage = $etudiant->getStageFor($session);
                if ($stage) {
                    $affectation = $stage->getAffectationStage();
                    if (isset($affectation) && $affectation->hasEtatValidee()) {
                        throw new Exception( sprintf("Le stage %s de %s a une affectation validée par la commission et ne peux donc pas être supprimé", $stage->getLibelle(), $stage->getEtudiant()->getDisplayName()));
                    }
                    $this->getStageService()->delete($stage);
                }
                $etudiant->removeSessionStage($session);
                $this->getObjectManager()->persist($etudiant);
                $this->getObjectManager()->persist($session);
//              On fait un premier flush ici pour valider la suppression des stages de l'étudiants
//              Requis car sinon il y a une boucle dans le mapping entre l'étudiant, le groupe, ces stages ...
                $this->getObjectManager()->flush();
            }
            $groupe->removeEtudiant($etudiant);
        }
        $this->getObjectManager()->persist($groupe);
        $this->getObjectManager()->flush();
        foreach ($etudiants as $etudiant) {
            $this->getStageService()->recomputeOrdresStage(null, $etudiant);
        }
        $this->getEtudiantService()->updateEtats($etudiants);
        return $groupe;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return $this
     * @throws OptimisticLockException|\Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null): static
    {
        /** @var SessionStage $session */
        foreach ($entity->getSessionsStages() as $session) {
            /** @var Stage $stage */
            foreach ($session->getStages() as $stage) {
                $affectation = $stage->getAffectationStage();
                if (isset($affectation) && $affectation->hasEtatValidee()) {
                    throw new Exception(sprintf("Le stage %s de %s a une affectation validée par la commission et ne peux donc pas être supprimé", $stage->getLibelle(), $stage->getEtudiant()->getDisplayName()));
                }
            }
        }

        $this->getObjectManager()->remove($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->recomputeOrdresGroupes();
        }
        return $this;
    }


    /**
     * @desc Retourne la liste des étudiants qui peuvent être inscrit dans le groupe
     * Un étudiant peut être inscrit dans un groupe si
     * TODO : a revoir s'il ne faut pas plutot faire un validateur sur les actions d'ajout / suppression et filtrer la listes par ce validateur
     */
    public function findEtudiantsCanBeAddInGroupe(Groupe $groupe, ?array $etudiants=null) : array
    {
        $annee = $groupe->getAnneeUniversitaire();
        $groupes = $annee->getGroupes();
        if(!isset($etudiants)){
            $etudiants = $this->getObjectManager()->getRepository(Etudiant::class)->findAll();
        }
        return array_filter($etudiants, function (Etudiant $etudiant) use ($groupes) {
            foreach ($groupes as $g){
                if($etudiant->getGroupes()->contains($g)){
                    return false;
                }
            }
            return true;
        });
    }

    //On ne peut retirer du groupe que des étudiants n'ayant pas d'affectation de stage validé
    public function findEtudiantsCanBeRemoveFromGroupe(Groupe $groupe) : array
    {
        $etudiants = $groupe->getEtudiants()->toArray();
        return array_filter($etudiants, function (Etudiant $etudiant) use ($groupe) {
            foreach ($etudiant->getStages() as $stage) {
                /** @var AffectationStage $affectation */
                $affectation = $stage->getAffectationStage();
                if (!$affectation || !$affectation->hasEtatValidee()) {
                    continue;
                }
                if ($stage->getSessionStage()->getGroupe()->getId() != $groupe->getId()) {
                    continue;
                }
                return false;
            }
            return true;
        });
    }

    use SessionStageServiceAwareTrait;

    /**
     * @return $this
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function recomputeOrdresGroupes() : static
    {
        $groupes = $this->getObjectRepository()->findAll();
        $groupes = Groupe::sortGroupes($groupes);
        /** @var Groupe|null $previous */
        $previous = null;
        $hasChange = false;
        /** @var Groupe $g */
        foreach ($groupes as $g){
            if($g->getGroupePrecedent() !== $previous) {
                $g->setGroupePrecedent($previous);
                $this->getObjectManager()->persist($g);
                $hasChange = true;
            }
            $previous = $g;
        }
        if(!$hasChange){//Pas de changement dans l'ordres des groupes, inutile de faire d'autres changement
            return $this;
        }
        $groupes = array_reverse($groupes);
        $next = null;
        foreach ($groupes as $g){
            $g->setGroupeSuivant($next);
            $this->getObjectManager()->persist($g);
            $next = $g;
        }

        $this->getObjectManager()->flush();
        $this->getSessionStageService()->recomputeOrdresSessions();
        return $this;
    }

}