<?php

namespace Application\Validator\Actions;

use Application\Controller\Groupe\GroupeController;
use Application\Controller\Stage\StageController;
use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Laminas\Validator\AbstractValidator;

/**
 * Validatateur permettant de vérifier si l'on peut ajouter ou supprimer un stages
 * Permet de faire la vérifications dans l'assertion, mais aussi dans le controlleur pour filtrer la listes des stages / étudians pour les actions
 * !!! il ne s'agit pas d'une assertion, pas de controle sur le role ..., uniquement sur les entités
 * Amélioration possible : comme pour validationStage de fournir l'explication, si utile
 */
class GroupeValidator extends AbstractValidator
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
            GroupeController::ACTION_AJOUTER_ETUDIANTS => $this->assertAjouter($this->groupe, $this->etudiant),
            GroupeController::ACTION_RETIRER_ETUDIANTS => $this->assertRetirer($this->groupe, $this->etudiant),
            default => false,
        };
    }

    public function assertAjouter(?Groupe $groupe, ?Etudiant $etudiant = null) : bool
    {
        if(!isset($groupe)){return false;}
        if(!isset($etudiant)){return false;}

        $annee = $groupe->getAnneeUniversitaire();
        $groupes = $annee->getGroupes();
        //L'étudiant ne doit pas déjà être inscrit dans un groupe pour l'année en question
        foreach ($groupes as $g2){
            if($etudiant->getGroupes()->contains($g2)){
                return false;
            }
        }
        return true;
    }

    public function assertRetirer(?Groupe $groupe, ?Etudiant $etudiant = null) : bool
    {
        if(!isset($groupe)){return false;}
        if(!isset($etudiant)){return false;}

        //L'étudiant ne doit pas avoir d'affectations validé pour un stage de concenant le groupe en question
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
    }

    use HasEtudiantTrait;
    use HasGroupeTrait;

    public function setData(array $data = []) : static
    {
        $this->setGroupe($data['groupe'] ?? null);
        $this->setEtudiant($data['etudiant'] ?? null);
        return $this;
    }

}
