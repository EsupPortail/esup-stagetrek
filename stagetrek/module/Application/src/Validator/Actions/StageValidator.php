<?php

namespace Application\Validator\Actions;

use Application\Controller\Stage\StageController;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Laminas\Validator\AbstractValidator;

/**
 * Validatateur permettant de vérifier si l'on peut ajouter ou supprimer un stages
 * Permet de faire la vérifications dans l'assertion, mais aussi dans le controlleur pour filtrer la listes des stages / étudians pour les actions
 * !!! il ne s'agit pas d'une assertion, pas de controle sur le role ..., uniquement sur les entités
 * Amélioration possible : comme pour validationStage de fournir l'explication, si utile
 */
class StageValidator extends AbstractValidator
{
    /**
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid(mixed $value, array $context = []) : bool
    {
        $this->setData($context);
        return match ($value) {
            StageController::ACTION_AJOUTER_STAGES => $this->assertAjouter($this->sessionStage, $this->etudiant),
            StageController::ACTION_SUPPRIMER_STAGES => $this->assertSupprimer($this->sessionStage, $this->etudiant),
            default => false,
        };
    }

    public function assertAjouter(?SessionStage $session, ?Etudiant $etudiant = null) : bool
    {
        if(!isset($session)){return false;}
        if(isset($etudiant)){
//            L'étudiant ne doit pas être inscrit à la sessions
            if($etudiant->getSessionsStages()->contains($session)){
                return false;
            }
            $groupe = $session->getGroupe();
//            L'étudiant doit être inscrit dans le groupe de la session
            if(!$etudiant->getGroupes()->contains($groupe)){ return false;}
//            L'étudiant ne doit pas avoir de stage pour la session
            $stage = $etudiant->getStageFor($session);
            return !isset($stage);
        }

        //Cas ou on ne précise pas l'étudiant = on regarde si l'on peut ajouter au moins 1 stage pour 1 étudiants
        $groupe = $session->getGroupe();
        $etudiants = $groupe->getEtudiants();
        //est-ce qu'il existes aux moins 1 étudiants dans le groupe qui n'as pas de stage pour la session
        /** @var Etudiant $etudiant */
        foreach ($etudiants as $etudiant) {
            if($this->assertAjouter($session, $etudiant)){ return true;}
        }
        return false;
    }

    public function assertSupprimer(?SessionStage $session, ?Stage $stage = null) : bool
    {
        if(!isset($session)){return false;}
        if(isset($stage)){
            //Un stage secondaire est supprimé automatiquement lors de la validation de l'affectations
            if($stage->isStageSecondaire()){return false;}
            $etudiant = $stage->getEtudiant();
            //Cas d'erreur que l'on souhaite pouvoir corriger en supprimant le stage
            if (!$etudiant->getSessionsStages()->contains($session)) {
                return true;
            }
            if (!$session->getEtudiants()->contains($etudiant)) {
                return true;
            }

            $affectation = $stage->getAffectationStage();
            //Pas d'affectation existantes
            if (!isset($affectation)) {
                return true;
            }
            //L'étudiant ne doit pas avoir d'affectation validé pour la sessio
            if ($affectation->hasEtatValidee()) {
                return false;
            }
            return true;
        }
        $stages = $session->getStages();
        //est-ce qu'il existes aux moins 1 étudiants dans le groupe qui n'as pas de stage pour la session
        /** @var Stage $stage */
        foreach ($stages as $stage) {
            if($this->assertSupprimer($session, $stage)){ return true;}
        }
        return false;
    }

    use HasStageTrait;
    use HasSessionStageTrait;
    use HasEtudiantTrait;

    public function setData(array $data = []) : static
    {
        $this->setSessionStage($data['session'] ?? null);
        $this->setStage($data['stage'] ?? null);
        $this->setEtudiant($data['etudiant'] ?? null);
        return $this;
    }

}
