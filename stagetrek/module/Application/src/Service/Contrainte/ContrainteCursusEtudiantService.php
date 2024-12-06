<?php

namespace Application\Service\Contrainte;

use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Provider\EtatType\ContrainteCursusEtudiantEtatTypeProvider;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use DateTime;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;

/**
 * Class ContrainteCursusService
 * @package Application\Service\ContraintesService
 */
class ContrainteCursusEtudiantService extends CommonEntityService
{
    use EtudiantServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return ContrainteCursusEtudiant::class;
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
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $entity;
        $this->getObjectManager()->persist($contrainte);
        if($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->computeEtat($contrainte);
            $this->getEtudiantService()->updateEtat($contrainte->getEtudiant());
        }
        return $entity;
    }

    /**
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return $this
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $entity;
        $etudiant = $contrainte->getEtudiant();
        $this->getObjectManager()->remove($entity);
        if($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($entity);
            $this->getEtudiantService()->updateEtat($etudiant);
        }
        return $entity;
    }

    /**
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $entity;
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($contrainte);
            $oldEtat = $contrainte->getEtatActif();
            $this->updateEtat($contrainte);
            //On ne met a jours l'état de l'étudiant que si l'état de la contraint à changé
            //Evite des cycles
            $newEtat = $contrainte->getEtatActif();
            if(!isset($oldEtat) || $oldEtat->getType()->getId() != $newEtat->getType()->getId()) {
                $this->getEtudiantService()->updateEtat($contrainte->getEtudiant());
            }
        }
        return $entity;
    }

    /**
     * @param ContrainteCursusEtudiant $contrainte
     * @return ContrainteCursusEtudiant
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function validerContrainte(ContrainteCursusEtudiant $contrainte): ContrainteCursusEtudiant
    {
        $contrainte->setValideeCommission(true);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($contrainte);
            $this->updateEtat($contrainte);
            $this->getEtudiantService()->updateEtat($contrainte->getEtudiant());
        }
        return $contrainte;
    }

    /**
     * @param ContrainteCursusEtudiant $contrainte
     * @return ContrainteCursusEtudiant
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function invaliderContrainte(ContrainteCursusEtudiant $contrainte): ContrainteCursusEtudiant
    {
        $contrainte->setValideeCommission(false);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($contrainte);
            $this->updateEtat($contrainte);
            $this->getEtudiantService()->updateEtat($contrainte->getEtudiant());
        }
        return $contrainte;
    }

    /**
     * @param ContrainteCursusEtudiant $contrainte
     * @return ContrainteCursusEtudiant
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function activerContrainte(ContrainteCursusEtudiant $contrainte): ContrainteCursusEtudiant
    {
        $contrainte->setActive(true);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($contrainte);
            $this->updateEtat($contrainte);
            $this->getEtudiantService()->updateEtat($contrainte->getEtudiant());
        }
        return $contrainte;
    }

    /**
     * @param ContrainteCursusEtudiant $contrainte
     * @return ContrainteCursusEtudiant
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function desactiverContrainte(ContrainteCursusEtudiant $contrainte): ContrainteCursusEtudiant
    {
        $contrainte->setActive(false);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($contrainte);
            $this->updateEtat($contrainte);
            $this->getEtudiantService()->updateEtat($contrainte->getEtudiant());
        }
        return $contrainte;
    }

    /**
     * @param Etudiant $etudiant
     * Fonction qui calcule (via les vue SQL) les recommandations pour les catégorie et les terrains de stages le nombre de fois qu'il est actuellement conseiller d'effectuer un stage dans la catégorie/terrain
     * on passe par une vue ici car il s'agit de données qui seront peux utilisé, calculable automatiquement,
     * rajoute des propriété publique temporaire au terrain de stage et au catégorie
     * @return Etudiant
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function computeRecommandationsContraintes(Etudiant $etudiant) : Etudiant
    {
        $recoCategories = [];
        $recoTerrains = [];
        $contraintes = $etudiant->getContraintesCursusEtudiants();
        /** @var ContrainteCursusEtudiant $contrainte */
        foreach ($contraintes as $contrainte) {
            if($contrainte->isInContradiction()){continue;}
            if($contrainte->isValideeCommission()){continue;}
            if($contrainte->isSat()){continue;}
            if(!$contrainte->isActive()){continue;}
            $reste = $contrainte->getResteASatisfaire();
            if($reste<=0){continue;}
            if($contrainte->hasPorteeCategorie()){
                $cat = $contrainte->getCategorieStage();
                $recoCategories[$cat->getId()] = $reste + (($recoCategories[$cat->getId()]) ?? 0);
            }
            elseif($contrainte->hasPorteeTerrain()){
                $t = $contrainte->getTerrainStage();
                $recoTerrains[$t->getId()] = $reste + (($recoTerrains[$t->getId()]) ?? 0);
            }

        }
        /** @var CategorieStage $categorie */
        $categories = $this->getObjectManager()->getRepository(CategorieStage::class)->findBy([],['ordre' => 'ASC', 'libelle'=>'ASC']);
        foreach ($categories as $categorie){
            if(isset($recoCategories[$categorie->getId()])){
                $nb = $recoCategories[$categorie->getId()];
                $etudiant->setRecommandationCategorie($categorie, $nb);
                $terrains = $categorie->getTerrainsStages()->toArray();
                $terrains = TerrainStage::sort($terrains);
                foreach ($terrains as $terrain){
                    if(isset($recoTerrains[$terrain->getId()])){
                        $nb = $recoTerrains[$terrain->getId()];
                        $etudiant->setRecommandationTerrain($terrain, $nb);
                    }
                }
            }
        }
        return $etudiant;
    }

    use EntityEtatServiceAwareTrait;
    public  function updateEtat(HasEtatsInterface $entity): HasEtatsInterface
    {
        if(!$entity instanceof ContrainteCursusEtudiant){
            throw new Exception("L'entité fournise n'est pas une contrainte de cursus etudiant.");
        }
        $contrainte = $entity;

        $this->getObjectManager()->refresh($contrainte);
        $this->updateComputedValues($contrainte);
        $this->setEtatInfo(null);
        $codeEtat = $this->computeEtat($contrainte);
        $this->setEtatActif($contrainte, $codeEtat);
        $etat = $contrainte->getEtatActif();
        $etat->setInfos(($this->etatInfo != "")? $this->etatInfo : null);
        $this->getEtatInstanceService()->update($etat);
        $this->getObjectManager()->refresh($contrainte);
        return $contrainte;
    }

    protected function computeEtat(HasEtatsInterface $entity): string
    {
        /** @var ContrainteCursusEtudiant $contrainte */
        $contrainte = $entity;
        if(!$contrainte->isActive()){
            $this->setEtatInfo("La contrainte a été désactivée pour l'étudiant");
            return ContrainteCursusEtudiantEtatTypeProvider::DESACTIVE;
        }
        if($contrainte->isValideeCommission()){
            $this->setEtatInfo("La contrainte à été validée par la comission");
            return ContrainteCursusEtudiantEtatTypeProvider::VALIDE_COMMISSION;
        }

        if($contrainte->isInContradiction()){
            $this->setEtatInfo("La contrainte est en contradiction avec au moins 1 autre contrainte de cursus");
            return ContrainteCursusEtudiantEtatTypeProvider::EN_ERREUR;
        }
//        Insat car il y a trop de stages de fait
        $max = $contrainte->getMax();
        if(!isset($max) || $max <= 0){$max = PHP_INT_MAX;}
        if($max < $contrainte->getNbStagesValidant()){
            $this->setEtatInfo("Le nombre maximum de stage autorisé a été dépasse");
            return ContrainteCursusEtudiantEtatTypeProvider::INSAT;
        }
        if($contrainte->isSat()){
            return ContrainteCursusEtudiantEtatTypeProvider::SAT;
        }
        //TODO : mettre en warning si le nombre de stage restant à satisfaire est proche du nombre de futur stage possible
        //TODO : mettre en INSAT si le nombre de stage restant à satisfaire est supérieur au nombre de futur possible

        return ContrainteCursusEtudiantEtatTypeProvider::NON_SAT;

    }

    //Fonction qui met a jours les valieurs pré-calculer : nombre de stages validant ...
    protected function updateComputedValues(ContrainteCursusEtudiant $contrainte) : ContrainteCursusEtudiant
    {
        $etudiant = $contrainte->getEtudiant();
        $stages = $etudiant->getStages()->toArray();
        $nbValidant = 0;
        $min = $contrainte->getMin();
        $max = $contrainte->getMax();
        if(!isset($min)){$min = 0;}
        if(!isset($max) || $max ==0){$max = PHP_INT_MAX;}

        $today = new DateTime();
        /** @var Stage $stage */
        foreach ($stages as $stage) {
            if($stage->isNonEffectue()){continue;}
//            Même s'il est validé, on ne compte pas le stage temps qu'il n'est pas finie
            if($today < $stage->getDateFinStage()){continue;}

            $affectation = $stage->getAffectationStage();
            if(!isset($affectation)){continue;} // pas d'affectation = stage ne comptant pas pour les contraintes
            if(!$affectation->isValidee()){continue;}
            $terrain = $stage->getTerrainStage();
            $categorie = $stage->getCategorieStage();
            if(!isset($categorie) || !isset($terrain)){continue;}
            //Vérification sur la portée
            if($contrainte->hasPorteeTerrain() && $contrainte->getTerrainStage()->getId() != $terrain->getId()){continue;}
            if($contrainte->hasPorteeCategorie() && $contrainte->getCategorieStage()->getId() != $categorie->getId()){continue;}

            $validation = $stage->getValidationStage();
            //stage pas encore validé = ne peux pas compté pour la satisfactions des contraintes
            if(isset($validation) && $validation->isValide()  && $stage->hasEtatValide()){$nbValidant++;}
        }

        //calcul du isSat
        $nbEquivalence = $contrainte->getNbEquivalences();
        $nbValidantAmelioree = $nbValidant + $nbEquivalence;
        //Pour ne pas dépasser la borneMax a cause du nombre d'équivalence
        if($nbValidantAmelioree > $max && $nbValidant <= $max){
            $nbValidantAmelioree  = $max;
        }
        $isSat = ($min <= $nbValidantAmelioree && $nbValidantAmelioree <= $max);
        $reste = max($min-$nbValidant-$nbEquivalence,0);

        $contrainte->setIsSat($isSat);
        $contrainte->setResteASatisfaire($reste);
        $contrainte->setNbStagesValidant($nbValidant);
        return $contrainte;
    }
}

