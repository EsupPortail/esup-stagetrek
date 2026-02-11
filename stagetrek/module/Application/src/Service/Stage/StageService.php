<?php

namespace Application\Service\Stage;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\ValidationStage;
use Application\Provider\EtatType\StageEtatTypeProvider;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use DateTime;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;

/**
 * Class StageService
 * @package Application\Service\Stage
 */
class StageService extends CommonEntityService
{
    use EtudiantServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return Stage::class;
    }

    /**
     * @param mixed $entity
     * @param null $serviceEntityClass classe de l'entité
     * @return Stage
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function add(mixed $entity, $serviceEntityClass = null) : mixed
    {
        /** @var Stage $stage */
        $stage = $entity;
        $this->getObjectManager()->persist($stage);
        if ($this->hasUnitOfWorksChange()) {
            //TODO : fonctions a revoir
            $this->updateStages();
            $this->getObjectManager()->refresh($stage);
            $this->recomputeOrdresStage(null, $stage->getEtudiant());

            $this->getSessionStageService()->updateEtat($stage->getSessionStage());
            $this->getEtudiantService()->updateEtat($stage->getEtudiant());

        }
        return $entity;
    }


    /**
     * @param array $entities
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function addMultiple(array $entities = [], string $serviceEntityClass = null): static
    {
        /** @var Stage[] $stages */
        $stages = $entities;
        /** @var Stage $stage */
        foreach ($stages as $stage){
            $this->getObjectManager()->persist($stage);
        }
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($stages);
            //TODO : fonctions a revoir
            $this->updateStages();

            $this->updateEtats($stages);
            /** @var Stage $stage */
            foreach ($stages as $stage) {
                $this->recomputeOrdresStage(null, $stage->getEtudiant());
                $this->getSessionStageService()->updateEtat($stage->getSessionStage());
                $this->getEtudiantService()->updateEtat($stage->getEtudiant());
//                L'acces au service d'affectation est celui de la sessions pour éviter des boucles infini dans les constructeurs
                $this->getSessionStageService()->getAffectationStageService()->updateEtat($stage->getAffectationStage());
            }
        }
        return $this;
    }


    /**
     * Ajoute/Met à jour une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null) : mixed
    {
        /** @var Stage $stage */
        $stage = $entity;
        $this->getObjectManager()->persist($stage);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            //TODO : fonctions a revoir
            $this->updateStages();
            $this->getObjectManager()->refresh($stage);

            $this->getSessionStageService()->updateEtat($stage->getSessionStage());
            $this->getEtudiantService()->updateEtat($stage->getEtudiant());

        }
        return $entity;
    }


    /**
     * Ajoute/Met à jour une entité
     *
     * @param Stage[] $stages
     * @return self
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    //mise à jours des ordres d'affectations des stages qui entraine le recalcul
    public function updateOrdresAffectations(SessionStage $sessionStage) : static
    {
        $this->execProcedure("recompute_ordre_affectation",[$sessionStage->getId()]);
        return $this;
    }


    //mise à jours des ordres d'affectations automatique
    //Doublon avec la fonction dans session stage service, a revoir lors du passage en API Plateform
    //A ne faire qu'une seul fois ! lorsque l'on est dans la bonne période si le calcul n'as pas encore été fait
    /**
     * @throws \Exception
     */
    public function updateOrdresAffectationsAuto(SessionStage $sessionStage) : static
    {
        $this->execProcedure("recompute_ordre_affectation_auto",[$sessionStage->getId()]);
        return $this;
    }


    /**
     * @param Etudiant $etudiant
     * @param SessionStage $session
     * @return Stage
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function createStage(Etudiant $etudiant, SessionStage $session) : Stage
    {//TODO : a revoir , ne fait aucun push
        //Quelques vérification pour éviter des problème
        //L'étudiant est déjà inscrit à la session
        if ($session->getEtudiants()->contains($etudiant)) {
            throw new Exception("L'étudiant est déjà inscrit dans la session de stage");
        }
        //L'étudiant n'appartient pas au groupe de la session
        if (!$etudiant->getGroupes()->contains($session->getGroupe())) {
            throw new Exception("L'étudiant n'est pas inscrit dans le groupe de la session");
        }

        //Création du stage
        $stage = new Stage();
        $stage->setEtudiant($etudiant);
        $stage->setSessionStage($session);
        $etudiant->addStage($stage);
        $session->addEtudiant($etudiant);
        //Création de l'affectation
        $affectation = new AffectationStage();
        $affectation->setStage($stage);
        $stage->setAffectationStage($affectation);
        //Création de la validation
        $validationStage = new ValidationStage();
        $validationStage->setStage($stage);
        $stage->setValidationStage($validationStage);
        return $stage;
    }

    //TODO: procédure sql a décomposer en PHP

    /**
     * @throws \Exception
     */
    public function updateStages() : void
    {
        $this->execProcedure("update_stages");
//        On recherche les stages n'ayant pas encore d'état pour calculer leurs états
        $qb = $this->getObjectRepository()->createQueryBuilder($alias = 's');
        $qb = $qb
            ->leftJoin('s.etats', 'etat')->addSelect('etat')
            ->leftJoin('etat.type', 'type')->addSelect('type')
            ->andWhere('etat.histoDestruction IS NULL')
            ->andWhere('etat.id is null');

        $stages = $qb->getQuery()->getResult();
        $this->updateEtats($stages);
    }


    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        /** @var Stage $stage */
        $stage = $entity;
        $affectation = $stage->getAffectationStage();
        if (isset($affectation) && $affectation->hasEtatValidee()) {
            throw new Exception(sprintf("Le stage %s a une affectation validé et ne peux donc pas être supprimé", $stage->getLibelle()));
        }
        // Retrait du lien entre l'étudiant et la session de stage nécessaire
        $stage->getEtudiant()->removeSessionStage($stage->getSessionStage());
        $etudiant = $stage->getEtudiant();
        $session = $stage->getSessionStage();
//        Pour éviter des pb de cycles;
        $stage->setStagePrecedent();
        $stage->setStageSuivant();
        $this->getObjectManager()->remove($stage);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            //TODO : fonctions a revoir
            $this->updateStages();
            $this->getObjectManager()->refresh($etudiant);
            $this->recomputeOrdresStage(null, $etudiant);

            $this->getSessionStageService()->updateEtat($session);
            $this->getEtudiantService()->updateEtat($etudiant);
        }
        return $this;
    }

    /**
     * @param Stage[] $entities
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function deleteMultiple(array $entities, string $serviceEntityClass = null) : static
    {
        $etudiants = [];
        $sessions = [];
        foreach ($entities as $stage){
            $affectation = $stage->getAffectationStage();
            if (isset($affectation) && $affectation->hasEtatValidee()) {
                throw new Exception(sprintf("Le stage %s de %s a une affectation validée par la commission et ne peux donc pas être supprimé", $stage->getLibelle(), $stage->getEtudiant()->getDisplayName()));
            }
            $etudiant = $stage->getEtudiant();
            $etudiants[] = $etudiant;
            $sessions[] = $stage->getSessionStage();
            // Retrait du lien entre l'étudiant et la session de stage nécessaire
            $etudiant->removeSessionStage($stage->getSessionStage());
            $stage->setStagePrecedent();
            $stage->setStageSuivant();
            $this->getObjectManager()->remove($stage);
        }
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            //TODO : fonctions a revoir
            $this->updateStages();

            foreach ($etudiants as $e){
                $this->recomputeOrdresStage(null, $e);
            }
            $this->getSessionStageService()->updateEtats($sessions);
            $this->getEtudiantService()->updateEtats($etudiants);
        }
        return $this;
    }



    /**
     * @return $this
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function recomputeOrdresStage(?SessionStage $session = null, ?Etudiant $etudiant = null) : static
    {
        if(!isset($session) && !isset($etudiant)){
            $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
            foreach ($sessions as $s){
                $this->recomputeOrdresStage($s);
            }
            return $this;
        }
        if(!isset($etudiant)){
            $etudiants = $session->getEtudiants();
            foreach ($etudiants as $e){
                $this->recomputeOrdresStage($session, $e);
            }
            return $this;
        }

        $stages = $etudiant->getStages()->toArray();
        //On filtre les stages pour ne pas prendre les stages sescondaires
        $stages = array_filter($stages, function(Stage $stage){
            return $stage->isStagePrincipal();
        });
        usort($stages, function(Stage $s1, Stage $s2){
          if($s1->getDateDebutStage() < $s2->getDateDebutStage()){ return -1;}
          if($s1->getDateDebutStage() > $s2->getDateDebutStage()){ return 1;}
//          Théoriquement pas possible
          if($s1->getDateFinStage() < $s2->getDateFinStage()){ return -1;}
          if($s1->getDateFinStage() > $s2->getDateFinStage()){ return 1;}
          return ($s1->getId() < $s2->getId()) ? -1 : 1;
        });
        /** @var Stage|null $previous */
        $previous = null;
        /** @var Stage $s */
        $num = 0;
        foreach ($stages as $s){
            $num++;
            $s->setStagePrecedent($previous);
            $s->setNumero($num);
            //Pour les stages secondaire
            if($s->hasStageSecondaire()){
                $s->setNumero($num+0.1);
                $s2 = $s->getStageSecondaire();
                $s2->setNumero($num+0.2);
                $s->setStageSuivant($s2);
                $s2->setStagePrecedent($s);
            }
//            $this->getObjectManager()->persist($s);
            $previous = (!$s->hasStageSecondaire()) ? $s : $s->getStageSecondaire();
        }
        $stages = array_reverse($stages);
        $next = null;
        foreach ($stages as $s){
            if(!$s->hasStageSecondaire()){
                $s->setStageSuivant($next);
            }
            else{
                $s2 = $s->getStageSecondaire();
                $s2->setStageSuivant($next);
            }
            $next = $s;
        }

        $this->getObjectManager()->flush($stages);
        return $this;
    }

    use EntityEtatServiceAwareTrait;
    protected function computeEtat(HasEtatsInterface $entity): string
    {
        if(!$entity instanceof Stage){
            throw new Exception("L'entité fournise n'est pas un stage.");
        }

        $stage = $entity;
        $etudiant = $stage->getEtudiant();
        //Etudiant en disponibilité
        $dispo = $etudiant->getDisponibilites();
        $today = new DateTime();

        /** @var Disponibilite $d */
        foreach ($dispo as $d){
            $debut = $d->getDateDebut();
            $fin = $d->getDateFin();
            if(($stage->getDateDebutStage() <= $debut && $debut < $stage->getDateFinStage())
                || ($stage->getDateDebutStage() <= $fin && $fin <= $stage->getDateFinStage())
                || ($debut <= $stage->getDateDebutStage() &&  $stage->getDateDebutStage() <= $fin)
                || ($debut <= $stage->getDateFinStage() &&  $stage->getDateFinStage() <= $fin)
            ){
                $this->setEtatInfo(sprintf("L'étudiant est en disponibilité du %s au %s", $debut->format('d/m/Y'), $fin->format('d/m/Y')));
                return StageEtatTypeProvider::EN_DISPO;
            }
        }

        //Stage non effectuée
        if($stage->isNonEffectue()){
            return StageEtatTypeProvider::NON_EFFECTUE;
        }

        //Plusieurs dates de stages se chevauche = stage mie en erreur
        $stages = $stage->getEtudiant()->getStages();
        /** @var Stage $s2 */
        foreach ($stages as $s2){
            if($s2->getid() == $stage->getId()){continue;}
            if($s2->isNonEffectue()){continue;}
            if($stage->hasStageSecondaire() && $stage->getStageSecondaire()->getId() == $s2->getId()){continue;}
            if($stage->hasStagePrincipal() && $stage->getStagePrincipal()->getId() == $s2->getId()){continue;}
            $debut = $s2->getDateDebutStage();
            $fin = $s2->getDateFinStage();
            if(($stage->getDateDebutStage() <= $debut && $debut < $stage->getDateFinStage())
                || ($stage->getDateDebutStage() <= $fin && $fin <= $stage->getDateFinStage())
                || ($debut <= $stage->getDateDebutStage() &&  $stage->getDateDebutStage() <= $fin)
                || ($debut <= $stage->getDateFinStage() &&  $stage->getDateFinStage() <= $fin)
            ){
                $this->setEtatInfo(sprintf("Le stage a lieu sur la même période que le stage n°%s", $s2->getNumero()));
                return StageEtatTypeProvider::EN_AlERTE;
            }
        }

        //Cas d'erreur :
        $affectation = $stage->getAffectationStage();
        $validation = $stage->getValidationStage();
        if(!isset($affectation)){
            $this->setEtatInfo("L'affectation de stage n'as pas été définie");
            return StageEtatTypeProvider::EN_ERREUR;
        }
        if(!isset($validation)){
            $this->setEtatInfo("Impossible de déterminer l'état de validation du stage");
            return StageEtatTypeProvider::EN_ERREUR;
        }

        if($validation->validationEffectue() && !$affectation->isValidee()){
            $this->setEtatInfo("Le stage est supposé être validé (ou invalidé), mais l'affectation n'a pas été validé par la commission");
            return StageEtatTypeProvider::EN_AlERTE;
        }
        if(!$affectation->isValidee() && $stage->getDateFinCommission() < $today){
            $this->setEtatInfo("L'affectation de stage n'as pas été validé par la commission");
            return StageEtatTypeProvider::EN_AlERTE;
        }

        //Pré-phase d'affectation (pour ne pas etre en erreur a cause de l'affectation avant la date d'affectation
        switch (true){
            case $today <= $stage->getDateDebutChoix():
                return StageEtatTypeProvider::FUTUR;
            case $stage->getDateDebutChoix() <= $today && $today < $stage->getDateFinChoix() :
                return StageEtatTypeProvider::PHASE_PREFERENCE;
            case $stage->getDateFinChoix() <= $today && $today < $stage->getDateFinCommission() :
                return StageEtatTypeProvider::PHASE_AFFECTATION;
            case $stage->getDateFinCommission() <= $today && $today < $stage->getDateDebutStage()
                && !$affectation->isValidee()
            : return StageEtatTypeProvider::PHASE_AFFECTATION;
        }

        if($affectation->hasEtatEnErreur()){
            $this->setEtatInfo("L'affectation du stage est en erreur");
            return StageEtatTypeProvider::EN_ERREUR;
        }
        if($stage->isStageSecondaire() && !$stage->hasStagePrincipal()){
            $this->setEtatInfo("Impossible de déterminer le stage principal correspondant");
            return StageEtatTypeProvider::EN_ERREUR;
        }

        //Cas d'une validation en ammont
        if($stage->hasValidationEffectuee()){
            if($today < $stage->getDateDebutValidation()){
                $this->addEtatInfo("La validation du stage est en avance sur la date planifiée.");
            }
//            return ($validation->isValide()) ? StageEtatTypeProvider::TERMINE_VALIDE : StageEtatTypeProvider::TERMINE_NON_VALIDE;
        }

        //Fonctionnement nominale
        switch (true){ //Aprés la phase d'affectation
          case $stage->getDateFinCommission() <= $today && $today < $stage->getDateDebutStage() :
                return StageEtatTypeProvider::A_VENIR;
            case $stage->getDateDebutStage() <= $today && $today < $stage->getDateFinStage() :
                return StageEtatTypeProvider::EN_COURS;

            //Le stage ne passe pas en validé avant la date de fin du stage
            case $stage->getDateFinStage() < $today && $stage->hasEvaluationEffectuee() && $stage->hasValidationEffectuee():
//                On se base pas sur l'état de la validation mais sur le fait qu'elle soit tagué fait/Non fait pour éviter des chevauchement d'état
                return ($validation->isValide()) ? StageEtatTypeProvider::TERMINE_VALIDE : StageEtatTypeProvider::TERMINE_NON_VALIDE;
            case $stage->getDateFinStage() <= $today && $today < $stage->getDateDebutValidation() :
                $this->addEtatInfo(sprintf("La phase de validation du stage commencera le %s", $stage->getDateDebutValidation()->format('d/m/Y')));
                return StageEtatTypeProvider::PHASE_VALIDATION;
            case $stage->getDateDebutValidation() <= $today && $today < $stage->getDateFinValidation() :
                return StageEtatTypeProvider::PHASE_VALIDATION;
            case $stage->getDateFinValidation() <= $today && !$validation->validationEffectue() :
                return StageEtatTypeProvider::VALIDATION_EN_RETARD;
//                TODO : a activer quand les évaluations seront en place
//            case $stage->getDateFinStage() <= $today && $today < $stage->getDateDebutEvaluation() && !$stage->hasEvaluationEffectuee():
//                $this->addEtatInfo(sprintf("La phase d'évaluation du stage commencera le %s", $stage->getDateDebutValidation()->format('d/m/Y')));
//                return StageEtatTypeProvider::PHASE_EVALUATION;
//            case $stage->getDateDebutEvaluation() <= $today && $today < $stage->getDateFinEvaluation() && !$stage->hasEvaluationEffectuee() :
//                return StageEtatTypeProvider::PHASE_EVALUATION;
//            case $today < $stage->getDateFinEvaluation() && !$stage->hasEvaluationEffectuee():
//                return StageEtatTypeProvider::EVALUATION_EN_RETARD;
        }

        $annee = $stage->getAnneeUniversitaire();
        if(!$annee->isLocked()){
            $this->setEtatInfo(sprintf("L'année universitaire %s est en cours de modification.", $annee->getLibelle()));
            return StageEtatTypeProvider::DESACTIVE;
        }

        $this->setEtatInfo("État indéterminée");
        return StageEtatTypeProvider::EN_ERREUR;
    }

}