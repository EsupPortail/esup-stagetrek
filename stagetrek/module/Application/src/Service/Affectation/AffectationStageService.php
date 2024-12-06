<?php

namespace Application\Service\Affectation;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Disponibilite;
use Application\Provider\EtatType\AffectationEtatTypeProvider;
use Application\Provider\Parametre\ParametreProvider;
use Application\Service\Contact\Traits\ContactStageServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use DateTime;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;

/**
 * Class AffectationStageService
 * @package Application\Service\AffectationStage
 */
class AffectationStageService extends CommonEntityService
{
    use EtudiantServiceAwareTrait;
    use StageServiceAwareTrait;
    use SessionStageServiceAwareTrait;
    use ContactStageServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return AffectationStage::class;
    }

    use ParametreServiceAwareTrait;

    /**
     * Ajoute d'une affectation de stage avec tout ce que celà implique
     *
     * @param mixed $entity
     * @param null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws Exception
     */
    public function add(mixed $entity, $serviceEntityClass = null): mixed
    {
        throw new Exception("L'ajout des affectations est automatique et ne peux pas passer par le service");
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
        throw new Exception("La suppression des affectations est automatique et ne peux pas passer par le service");
    }

    /**
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var AffectationStage $affectation */
        $affectation = $entity;
        //Maj du cout de l'affectation
        $max = $this->getParametreService()->getParametreValue(ParametreProvider::AFFECTATION_COUT_TOTAL_MAX);
        $cout = min($max, $affectation->getCoutTerrain() +
            $affectation->getBonusMalus());
        $affectation->setCout($cout);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($affectation);

            //TODO : fonctions a revoir, permet de gerer entre autres la création des stages secondaires
            $this->getStageService()->updateStages();
            $this->getObjectManager()->refresh($affectation->getStage());
            $this->getObjectManager()->refresh($affectation);
            $this->updatePreferenceSatisfaction($affectation);
            $this->updateEtat($affectation);
            $stage = $affectation->getStage();
            $this->getStageService()->updateEtat($stage);
            if($stage->hasStageSecondaire()){
                $this->getStageService()->updateEtat($stage->getStageSecondaire());
            }
            $this->getEtudiantService()->updateEtat($affectation->getEtudiant());
            $this->getContactStageService()->updateContactsStage();
            $this->getSessionStageService()->computePlacesForSessions();
        }
        return $entity;
    }

    /**
     * @param AffectationStage[] $entities
     * @return mixed
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function updateMultiple(array $entities) : array
    {
        $affectationsStages = $entities;
        $stages = [];
        $etudiants = [];
        foreach ($affectationsStages as $affectation) {
            //Maj du cout de l'affectationStage
            $max = $this->getParametreService()->getParametreValue(ParametreProvider::AFFECTATION_COUT_TOTAL_MAX);
            $cout = min($max, $affectation->getCoutTerrain() +
                $affectation->getBonusMalus());
            $affectation->setCout($cout);
            $etudiants[] = $affectation->getEtudiant();
            $stage = $affectation->getStage();
            $stages[] = $stage;
            if($stage->hasStageSecondaire()){
                $stages[] = $stage;
            }
        }
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            //TODO : fonctions a revoir, permet de gerer entre autres la création des stages secondaires
            $this->getStageService()->updateStages();
            $this->getObjectManager()->refresh($affectation->getStage());
            $this->getObjectManager()->refresh($affectation);

            foreach ($affectationsStages as $affectation) {
                $this->updatePreferenceSatisfaction($affectation);
            }
            $this->updateEtats($affectationsStages);
            $this->getStageService()->updateEtats($stages);
            $this->getEtudiantService()->updateEtats($etudiants);
            $this->getContactStageService()->updateContactsStage();
            $this->getSessionStageService()->computePlacesForSessions();
        }
        return $entities;
    }

    public function updatePreferenceSatisfaction(AffectationStage $affectation): void
    {//Calcul le rang de la préférence satisfaite (potentiellement utile pour des cas de changemùent manuel de préférence
        // Théoriquement ne devrais pas changer tout les 4 matins
        $terrain = $affectation->getTerrainStage();
        if(!isset($terrain)){
            $affectation->setRangPreference(null);
        }
        $preferences = $affectation->getStage()->getPreferences();
        $rang = null;
        /** @var \Application\Entity\Db\Preference $preference */
        foreach($preferences as $preference) {
            $t = $preference->getTerrainStage();
            if(isset($terrain) &&  isset($t) && $t->getId() == $terrain->getId()){
                $rang = $preference->getRang();
                $preference->setSat(true);
            }
            else{
                $preference->setSat(false);
            }
        }
        $affectation->setRangPreference($rang);
        $this->getObjectManager()->flush($affectation);
        foreach ($preferences as $p){
            $this->getObjectManager()->flush($p);
        }
    }


    use EntityEtatServiceAwareTrait;
//    TODO : a revoir : gerer les cas des dispos et des stages non effecuté pour qu'ils ne soient pas affecté a un terrain
    protected function computeEtat(HasEtatsInterface $entity): string
    {
        if(!$entity instanceof AffectationStage){
            throw new Exception("L'entité fournise n'est pas une affectation de stage.");
        }

        $affectation = $entity;
        $stage = $affectation->getStage();
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
                return AffectationEtatTypeProvider::EN_DISPO;
            }
        }

        //Stage non effectuée
        if($stage->isNonEffectue()){
            return AffectationEtatTypeProvider::STAGE_NON_EFFECTUE;
        }

        //Cas d'erreur : le stage est considéré comme validé, mais il n'y as pas de terrains
        $validation = $stage->getValidationStage();
        $terrain = $affectation->getTerrainStage();
        if(isset($validation) && $validation->validationEffectue() && !isset($terrain)){
            $this->setEtatInfo("Le stage est supposé être validé (ou invalidé), mais l'affectation n'a pas de terrain de stage");
            return AffectationEtatTypeProvider::EN_ERREUR;
        }
        //Cas d'erreur sur les terrains secondaire
        $terrainSecondaire = $affectation->getTerrainStageSecondaire();
        $stageSecondaire = $stage->getStageSecondaire();
        if(isset($terrainSecondaire) && !isset($stageSecondaire) && $affectation->isValidee()){
            $this->setEtatInfo("L'affectation implique l'existance d'un stage secondaire qui n'existe pas");
            return AffectationEtatTypeProvider::EN_ERREUR;
        }
        if(isset($stageSecondaire) && !isset($terrainSecondaire)){
            $this->setEtatInfo("L'étudiant a un stage secondaire mais pas de terrains secondaire affecté correspondant");
            return AffectationEtatTypeProvider::EN_ERREUR;
        }

//        Affectation validée, pré-validé, proposition
//        On en met pas l'affectation en erreur pour gerer des cas particuliée. par contre, on mettra automatiquement le stage en erreur
        switch (true){
            case isset($terrain) && $stage->getDateDebutStage() < $today && ! $affectation->isValidee():
                $this->addEtatInfo("L'affectation de l'étudiant n'est pas validée.");
                return AffectationEtatTypeProvider::EN_AlERTE;
            case isset($terrain) && $affectation->isValidee() :
                    return AffectationEtatTypeProvider::VAlIDEE;
            case isset($terrain) && $affectation->isPreValidee() :
                    return AffectationEtatTypeProvider::PRE_VAlIDEE;
            case isset($terrain) :
                return AffectationEtatTypeProvider::PROPOSTION;
//                Pas encore d'affectation :
            case $today < $stage->getDateCommission():
                return AffectationEtatTypeProvider::FUTUR;
            case $stage->getDateCommission() <= $today && $today < $stage->getDateFinCommission():
                return AffectationEtatTypeProvider::EN_COURS;
            case $stage->getDateFinCommission() <= $today &&  $today < $stage->getDateDebutStage():
                $this->addEtatInfo("La phase d'affectation est théoriqument terminée.");
                $this->addEtatInfo("L'étudiant n'as pas été affecté.");
                return AffectationEtatTypeProvider::EN_RETARD;
            case $stage->getDateDebutStage() <= $today:
                $this->addEtatInfo("L'étudiant n'as pas été affecté");
                return AffectationEtatTypeProvider::NON_AFFECTE;
        }
        $this->setEtatInfo("État indéterminée");
        return AffectationEtatTypeProvider::EN_ERREUR;
    }
}