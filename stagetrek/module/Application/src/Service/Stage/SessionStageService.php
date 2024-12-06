<?php

namespace Application\Service\Stage;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\SessionStageTerrainLinker;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStageNiveauDemande;
use Application\Form\Stages\SessionStageRechercheForm as FormRecherche;
use Application\Provider\DegreDemande\TerrainStageNiveauDemandeProvider;
use Application\Provider\EtatType\SessionEtatTypeProvider;
use Application\Service\Affectation\Traits\AffectationStageServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Application\Service\Stage\Traits\ValidationStageServiceAwareTrait;
use DateTime;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;

/**
 * Class SessionStageService
 * @package Application\Service\SessionStage
 */
class SessionStageService extends CommonEntityService
{
    use StageServiceAwareTrait;
    use EtudiantServiceAwareTrait;
    use AffectationStageServiceAwareTrait;
    use ValidationStageServiceAwareTrait;

    /**
     * @param array $criteria
     * @return SessionStage[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function search(array $criteria): array
    {
        $qb = $this->getObjectRepository()->createQueryBuilder($alias = 's');
        if (isset($criteria[FormRecherche::INPUT_ANNEE]) || isset($criteria[FormRecherche::INPUT_GROUPE])) {
            $qb->leftJoin("$alias.anneeUniversitaire", 'a');
        }
        if (isset($criteria[FormRecherche::INPUT_GROUPE])) {
            $qb->leftJoin("$alias.groupe", 'g');
        }

        if (!empty($criteria[FormRecherche::INPUT_LIBELLE])) {
            $qb->andWhere($qb->expr()->like($qb->expr()->upper("$alias.libelle"), $qb->expr()->upper(':libelle')));
            $qb->setParameter('libelle', "{$criteria[FormRecherche::INPUT_LIBELLE]}%");
        }

        if (isset($criteria[FormRecherche::INPUT_ANNEE])) {
            $where = $qb->expr()->in("a.id", ":annee");
            $qb->andWhere($where);
            $qb->setParameter('annee', $criteria[FormRecherche::INPUT_ANNEE]);
        }
        if (isset($criteria[FormRecherche::INPUT_GROUPE])) {
            $where = $qb->expr()->in("g.id", ":groupe");
            $qb->andWhere($where);
            $qb->setParameter('groupe', $criteria[FormRecherche::INPUT_GROUPE]);
        }
        if (!empty($criteria[FormRecherche::INPUT_ETAT])) {
            $qb = SessionStage::decorateWithEtats($qb, $alias, $criteria['etat']);
        }
        return $qb->getQuery()->getResult();
    }


    /** @return string */
    public function getEntityClass(): string
    {
        return SessionStage::class;
    }

    /**
     * @param int|Groupe $groupe
     * @return SessionStage[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByGroupe(int|Groupe $groupe): array
    {
        if ($groupe instanceof Groupe) $groupe = $groupe->getId();
        $result = $this->findAllBy(['groupe' => $groupe]);
        return $this->getList($result);
    }

    /**
     * @param int|AnneeUniversitaire $annee
     * @return SessionStage[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByAnnee(AnneeUniversitaire|int $annee): array
    {
        if ($annee instanceof AnneeUniversitaire) $annee = $annee->getId();
        $result = $this->getObjectRepository()->findBy(['anneeUniversitaire' => $annee], []);
        return $this->getList($result);
    }

    /**
     * @param mixed $entity
     * @param null $serviceEntityClass classe de l'entité
     * @return SessionStage
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, $serviceEntityClass = null) : SessionStage
    {
        /** @var SessionStage $session */
        $session = $entity;

        //Création de la session
        $this->getObjectManager()->persist($session);
        $this->getObjectManager()->flush($session);
        $this->recomputeOrdresSessions();
        $this->updateEtat($session);

        $this->getObjectManager()->refresh($session);
        $stages = [];
        //Création si besoins des stages pour les étudiants inscrit dans le groupe
        //On crée automatiquement le stage si celui-ci n'as pas encore commencé (et qu'il ne s'agit pas d'une session de rattrapagee
        if(new DateTime() < $session->getDateDebutStage()  && !$session->isSessionRattrapge()){
            foreach ($session->getGroupe()->getEtudiants() as $etudiant){
                $stage = $this->getStageService()->createStage($etudiant, $session);
                $this->getObjectManager()->persist($stage);
                $stages[] = $stage;
            }
        }

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($stages);
            $this->getObjectManager()->refresh($session);

            $this->getStageService()->recomputeOrdresStage($session);
            $this->getStageService()->updateEtats($stages);
            $affectations=[];
            $validationsStages=[];
            foreach ($stages as $stage){
                $affectations[]= $stage->getAffectationStage();
                $validationsStages[]= $stage->getValidationStage();
            }
            $this->getAffectationStageService()->updateEtats($affectations);
            $this->getValidationStageService()->updateEtats($validationsStages);

            $etudiants = $session->getEtudiants()->toArray();
            $this->getEtudiantService()->updateEtats($etudiants);
            $this->computePlacesForSessions(); // création des sessions stageTerrains linker entre autres
            $this->updateEtat($session);
        }
        return $session;
    }

    /**
     * Ajoute/Met à jour une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        $session = $entity;
        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $oldData = $unitOfwork->getOriginalEntityData($session);

        $this->getObjectManager()->persist($session);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->updateEtat($session);
            if((isset($oldData['dateDebutStage']) && $oldData['dateDebutStage'] != $session->getDateDebutStage())
                || (isset($oldData['dateFinStage']) && $oldData['dateFinStage'] != $session->getDateFinStage())
            ){
                $this->recomputeOrdresSessions();
                $this->getStageService()->recomputeOrdresStage($session);
                //un changement d'ordre peut modifier l'état de la session et donc de l'étudiant
                $etudiants = $session->getEtudiants()->toArray();
                $stages = $session->getStages()->toArray();
                $this->getStageService()->updateEtats($stages);
                $this->getEtudiantService()->updateEtats($etudiants);
                $affectations=[];
                $validationsStages=[];
                //potentiellement changments d'état suite à des changements de dates
                foreach ($stages as $stage){
                    $affectations[]= $stage->getAffectationStage();
                    $validationsStages[]= $stage->getValidationStage();
                }
                $this->getAffectationStageService()->updateEtats($affectations);
                $this->getValidationStageService()->updateEtats($validationsStages);
            }
            //Le changement des dates peux modifier les places dispobubles
            $this->computePlacesForSessions();
        }
        return $entity;
    }

    /**
     * Supprime une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {

        $sessions = $this->getObjectRepository()->findAll();
        $sessions = SessionStage::sortSessions($sessions);
        /** @var SessionStage $session */
        $session = $entity;
        /** @var Stage $stage */
        foreach ($session->getStages() as $stage){
            $affectation = $stage->getAffectationStage();
            if (isset($affectation) && $affectation->hasEtatValidee()) {
                throw new Exception("Le stage %s de %s a une affectation validée par la commission et ne peux donc pas être supprimé", $stage->getNumero(), $stage->getEtudiant()->getDisplayName());
            }
        }
        $this->getObjectManager()->remove($session);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->recomputeOrdresSessions();
            $this->getStageService()->recomputeOrdresStage();
        }
        return $this;
    }


    /**
     * @return $this
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function recomputeOrdresSessions() : static
    {
        $sessions = $this->getObjectRepository()->findAll();
        $sessions = SessionStage::sortSessions($sessions);
        /** @var SessionStage|null $previous */
        $previous = null;
        $hasChange = false;
        /** @var SessionStage $s */
        foreach ($sessions as $s){
            if($s->getSessionPrecedente() !== $previous) {
                $s->setSessionPrecedente($previous);
                $hasChange = true;
            }
            $previous = $s;
        }
        if(!$hasChange){//Pas de changement dans l'ordres des groupes, inutile de faire d'autres changement
            return $this;
        }
        $sessions = array_reverse($sessions);
        $next = null;
        foreach ($sessions as $s){
            $s->setSessionSuivante($next);
            $next = $s;
        }

        $this->getObjectManager()->flush($sessions);
        return $this;
    }


    /**
     * @throws \Exception
     */
    public function computePlacesForSessions() : static
    {
        //TODO : revoir le calcul pour ne plus le faire en SQL (rapide mais difficile a débuggué
        $this->execProcedure('update_sessions_stages_places');

       return $this;
    }

    /**
     * @return $this
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Exception
     */
    public function recomputeDemandeTerrains(SessionStage $sessionStage) : static
    {
        $this->execProcedure('recompute_niveau_demandes_terrains', ['sessionId' => $sessionStage->getId()]);
        return $this;
//        recompute_niveau_demandes_terrains

//      TODO : a revoir car trop gourmand en ressources => procédure sql
//        $etudiants = $sessionStage->getEtudiants();
//        //Pour chaque terrains de stage, on détermine le nombre demande / par le rang.
//        $demandes = [];
//        /** @var \Application\Entity\Db\Etudiant $etudiant */
//        foreach ($etudiants as $etudiant){
//            $terrainsSecondairesDemandees = [];
//            $preferences = $etudiant->getPreferenceForSession($sessionStage);
//
//            foreach ($preferences as $p){
//                $t = $p->getTerrainStage();
//                $rang = $p->getRang();
//                if(!isset($demandes[$t->getId()])){
//                    $demandes[$t->getId()][0] = 0;
//                }
//                if(!isset($demandes[$t->getId()][$rang])){
//                    $demandes[$t->getId()][$rang] = 0;
//                }
//                $demandes[$t->getId()][0]++;
//                $demandes[$t->getId()][$rang]++;
//                if($p->hasTerrainStageSecondaire()){
//                    $t =  $p->getTerrainStageSecondaire();
//                    if(!isset($terrainsSecondairesDemandees[$t->getId()])){
//                        $terrainsSecondairesDemandees[$t->getId()]=$rang;
//                    }
//                    $terrainsSecondairesDemandees[$t->getId()] = min($terrainsSecondairesDemandees[$t->getId()], $rang);
//                }
//            }
//            foreach ($terrainsSecondairesDemandees as $terrainId => $rang){
//                if(!isset($demandes[$terrainId])){
//                    $demandes[$terrainId][0] = 0;
//                }
//                if(!isset($demandes[$terrainId][$rang])){
//                    $demandes[$terrainId][$rang] = 0;
//                }
//                $demandes[$terrainId][0]++;
//                $demandes[$terrainId][$rang]++;
//            }
//        }
//
//        $paramCout = $this->getObjectManager()->getRepository(ParametreCoutAffectation::class)->findAll();
//        $terrainsNiveaux = $this->getObjectManager()->getRepository(TerrainStageNiveauDemande::class)->findAll();
//        $couts = [];
//        /** @var ParametreCoutAffectation $p */
//        foreach ($paramCout as $p){
//            $couts[$p->getRang()] = $p->getCout();
//        }
//        $niveaux = [];
//        /** @var TerrainStageNiveauDemande $n */
//        foreach ($terrainsNiveaux as $n){
//            $niveaux[$n->getCode()] = $n;
//        }
//        $scores=[];
//        $terrainsLinkers = $sessionStage->getTerrainLinker();
//
//        //Calcul séparer pour les terrains secondaires qui ont un classement a part
//        $terrainsDemande = [];
//        $terrainsSecondairesDemandees = [];
//        /** @var \Application\Entity\Db\SessionStageTerrainLinker $tl */
//        foreach ($terrainsLinkers as $tl){
//            $terrain = $tl->getTerrainStage();
//            $nbPlaces = $tl->getNbPlacesOuvertes();
//            // pas de places ouvertes
//            if($nbPlaces == 0){
//                $n = $niveaux[TerrainStageNiveauDemandeProvider::FERME];
//                $tl->setNiveauDemande($n);
//                $this->getObjectManager()->flush($tl);
//                continue;
//            }
////            Pas de demande
//            if(!isset($demandes[$terrain->getId()]) || $demandes[$terrain->getId()][0] == 0){
//                $n = $niveaux[TerrainStageNiveauDemandeProvider::NO_DEMANDE];
//                $tl->setNiveauDemande($n);
//                $this->getObjectManager()->flush($tl);
//                continue;
//            }
//            $scores[$terrain->getId()] = 0;
//            foreach ($demandes[$terrain->getId()] as $r => $nb){
//                if($r==0){continue;}
//                $c = max((isset($couts[$r])) ? $couts[$r] : 0, 0.1);
//                $scores[$terrain->getId()] += $nb*$c;
//            }
//            $scores[$terrain->getId()] /= $nbPlaces;
//            if($terrain->isTerrainPrincipal()){
//                $terrainsDemande[] = $tl;
//            }
//            else{
//                $terrainsSecondairesDemandees[] = $tl;
//            }
//        }
//
//        //On trie les terrains de stages par scores + un aléa en cas d'égalité
//        $seed = $sessionStage->getId()*1000;
//        mt_srand($seed);
//        usort($terrainsDemande, function (SessionStageTerrainLinker $tl1, SessionStageTerrainLinker $tl2)
//            use($scores, $seed)
//        {
//            $t1 = $tl1->getTerrainStage();
//            $t2 = $tl2->getTerrainStage();
//            $s1 = $scores[$t1->getId()];
//            $s2 = $scores[$t2->getId()];
//            while($s1 == $s2){ //Cas d'égalité = aléatoire
//                $s1 = mt_rand();
//                $s2 = mt_rand();
//            }
//            return ($s1>$s2)? 1 : -1;
//        });
//        usort($terrainsSecondairesDemandees, function (SessionStageTerrainLinker $tl1, SessionStageTerrainLinker $tl2)
//            use($scores, $seed)
//        {
//            $t1 = $tl1->getTerrainStage();
//            $t2 = $tl2->getTerrainStage();
//            $s1 = $scores[$t1->getId()];
//            $s2 = $scores[$t2->getId()];
//            while($s1 == $s2){ //Cas d'égalité = aléatoire
//                $s1 = mt_rand();
//                $s2 = mt_rand();
//            }
//            return ($s1>$s2)? 1 : -1;
//        });
//
//        $nbTerrainsDemandes = sizeof($terrainsDemande);
//        $cpt = $nbTerrainsDemandes;
//        foreach ($terrainsDemande as $tl){
//            $degres = intval(ceil($cpt/$nbTerrainsDemandes*5));
//            $cpt--;
//            $n = match($degres){
//                1 =>  TerrainStageNiveauDemandeProvider::RANG_1,
//                2 =>  TerrainStageNiveauDemandeProvider::RANG_2,
//                3 =>  TerrainStageNiveauDemandeProvider::RANG_3,
//                4 =>  TerrainStageNiveauDemandeProvider::RANG_4,
//                5 =>  TerrainStageNiveauDemandeProvider::RANG_5,
//                default => TerrainStageNiveauDemandeProvider::INDETERMINE
//            };
//            $tl->setNiveauDemande($niveaux[$n]);
//            $this->getObjectManager()->flush($tl);
//        }
//
//        $nbTerrainsDemandes = sizeof($terrainsSecondairesDemandees);
//        $cpt = $nbTerrainsDemandes;
//        foreach ($terrainsSecondairesDemandees as $tl){
//            $degres = intval(ceil($cpt/$nbTerrainsDemandes*5));
//            $cpt--;
//            $n = match($degres){
//                1 =>  TerrainStageNiveauDemandeProvider::RANG_1,
//                2 =>  TerrainStageNiveauDemandeProvider::RANG_2,
//                3 =>  TerrainStageNiveauDemandeProvider::RANG_3,
//                4 =>  TerrainStageNiveauDemandeProvider::RANG_4,
//                5 =>  TerrainStageNiveauDemandeProvider::RANG_5,
//                default => TerrainStageNiveauDemandeProvider::INDETERMINE
//            };
//            $tl->setNiveauDemande($niveaux[$n]);
//            $this->getObjectManager()->flush($tl);
//        }
//        return $this;
    }

//



    use EntityEtatServiceAwareTrait;
    protected function computeEtat(HasEtatsInterface $entity): string
    {
        if(!$entity instanceof SessionStage){
            throw new Exception("L'entité fournise n'est pas une session de stage.");
        }
        $session = $entity;
        $annee = $session->getAnneeUniversitaire();
        $today = new DateTime();

        //Désactivé si l'année n'est pas vérouillée
        if(!$annee->isAnneeVerrouillee()){ //on ne vérouille pas une session si la date de choix est commencé
            $this->addEtatInfo("L'année universitaire n'est pas validé");
            if($today < $session->getDateDebutChoix()) {
                return SessionEtatTypeProvider::DESACTIVE;
            }
        }
        //Aucun étudiant
        $etudiants = $session->getEtudiants()->toArray();
        if(empty($etudiants)){
            if($session->isSessionRattrapge()){
                $this->setEtatInfo("Session de rattrapage sans étudiants inscrits");
                return SessionEtatTypeProvider::DESACTIVE;
            }
            if($session->getDateDebutChoix() <= $today && $today <= $session->getDateFinChoix()){
                $this->setEtatInfo("Aucun étudiant n'est inscrit à cette session, la phase de définition des préférences est en cours");
                return SessionEtatTypeProvider::EN_AlERTE;
            }
            if($session->getDateFinChoix() <= $today && $today <= $session->getDateDebutStage()){
                $this->setEtatInfo("Aucun étudiant n'est inscrit à cette session, les stages vont thèoriquement bientôt commencer");
                return SessionEtatTypeProvider::EN_ERREUR;
            }
        }

        //Période non commencé
        if($today < $session->getDateCalculOrdresAffectations()){
            return SessionEtatTypeProvider::FUTUR;
        }

        //Cas d'alerte : places non définie
        $hasPlace = false;
        foreach ($session->getTerrainsStages() as $t){
           $nbPlace = $session->getNbPlacesOuvertes($t);
           if($nbPlace>0){$hasPlace = true; break;}
        }
        if(!$hasPlace && $session->getDateCalculOrdresAffectations() < $today && $today <= $session->getDateFinChoix()){
            $this->setEtatInfo("Le nombre de place(s) ouverte(s) n'a pas été défini");
            return SessionEtatTypeProvider::EN_AlERTE;
        }

        //Si certains stages sont en alerte
        //TODO : mettre en alerte ?

        //Fonctionnement nominale
        switch (true){
            case $today <= $session->getDateDebutChoix():
                return SessionEtatTypeProvider::FUTUR;
            case $session->getDateDebutChoix() <= $today && $today < $session->getDateFinChoix() :
                return SessionEtatTypeProvider::PHASE_PREFERENCE;
            case $session->getDateFinChoix() <= $today && $today < $session->getDateFinCommission() :
                return SessionEtatTypeProvider::PHASE_AFFECTATION;
            case $session->getDateFinCommission() <= $today && $today < $session->getDateDebutStage() :
                return SessionEtatTypeProvider::A_VENIR;
            case $session->getDateDebutStage() <= $today && $today < $session->getDateFinStage() :
                return SessionEtatTypeProvider::EN_COURS;
            case $session->getDateFinStage() <= $today && $today < $session->getDateDebutValidation() :
                $this->addEtatInfo(sprintf("La phase de validation des stages commencera le %s", $session->getDateDebutValidation()->format('d/m/Y')));
                return SessionEtatTypeProvider::PHASE_VALIDATION;
            case $session->getDateFinStage() <= $today && $today < $session->getDateFinValidation() :
                return SessionEtatTypeProvider::PHASE_VALIDATION;
//                TODO : a activer quand les évaluations seront en place
//            case $session->getDateFinStage() <= $today && $today < $session->getDateDebutEvaluation() :
//                $this->addEtatInfo(sprintf("La phase d'évaluation des stages commencera le %s", $session->getDateDebutValidation()->format('d/m/Y')));
//                return SessionEtatTypeProvider::PHASE_EVALUATION;
//            case $session->getDateDebutEvaluation() <= $today && $today < $session->getDateFinEvaluation() :
//                return SessionEtatTypeProvider::PHASE_EVALUATION;
            case $session->getDateFinValidation() <= $today :
                return SessionEtatTypeProvider::TERMINE;
        }

        $this->setEtatInfo("Etat indéterminée");
        return SessionEtatTypeProvider::EN_ERREUR;

    }

}