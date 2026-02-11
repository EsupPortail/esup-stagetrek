<?php

namespace Application\Service\Renderer;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use DateTime;

class DateRendererService
{

    /** @var array */
    protected array $variables = [];

    /**
     * @param array $variables
     * @return DateRendererService
     */
    public function setVariables(array $variables): DateRendererService
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getVariable(string $key): mixed
    {
        if (!isset($this->variables[$key])) return null;
        return $this->variables[$key];
    }

    public function formatDate(DateTime $date): string
    {
        return $date->format('d/m/Y');
    }

    public function formatTime(DateTime $date): string
    {
        return $date->format('H\hi');
    }

    public function formatAnnee(DateTime $date): string
    {
        return $date->format('Y');
    }


    /** Etudiant */
    public function getDateNaissanceEtudiant(): ?string
    {
        /** @var Etudiant $etudiant */
        $etudiant = $this->getVariable('etudiant');
        if ($etudiant === null) return null;
        return $this->formatDate($etudiant->getDateNaissance());
    }

    /** AnneeUniversitaire */
    public function getDateDebutAnneeUniversitaitre(): ?string
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getVariable('anneeUniversitaire');
        if ($annee === null) return null;
        return $this->formatDate($annee->getDateDebut());
    }

    public function getDateFinAnneeUniversitaitre(): ?string
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getVariable('anneeUniversitaire');
        if ($annee === null) return null;
        return $this->formatDate($annee->getDateFin());
    }


    public function getAnneeDebutAnneeUniversitaitre(): ?string
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getVariable('anneeUniversitaire');
        if ($annee === null) return null;
        return $this->formatAnnee($annee->getDateDebut());
    }

    public function getAnneeFinAnneeUniversitaitre(): ?string
    {
        /** @var AnneeUniversitaire $annee */
        $annee = $this->getVariable('anneeUniversitaire');
        if ($annee === null) return null;
        return $this->formatAnnee($annee->getDateFin());
    }


    /** alias pour les session de stage pour les macros qui utilise l'ancienne méthode */
    public function getDateDebutSessionStage(): ?string
    {
        return $this->getDateDebutSession();
    }
    public function getDateFinSessionStage(): ?string
    {
        return $this->getDateFinSession();
    }

    /** Stages */
    public function getDateDebutStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateDebutStage());
    }

    public function getHeureDebutStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateDebutStage());
    }

    public function getDateFinStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateFinStage());
    }

    public function getHeureFinStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateFinStage());
    }

    public function getDateDebutChoixStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateDebutChoix());
    }

    public function getHeureDebutChoixStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateDebutChoix());
    }

    public function getDateFinChoixStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateFinChoix());
    }

    public function getHeureFinChoixStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateFinChoix());
    }

    public function getDateCommissionStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateCommission());
    }

    public function getHeureCommissionStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateCommission());
    }

    public function getDateFinCommissionStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateFinCommission());
    }

    public function getHeureFinCommissionStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateFinCommission());
    }

    public function getDateDebutValidationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateDebutValidation());
    }

    public function getHeureDebutValidationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateDebutValidation());
    }

    public function getDateFinValidationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateFinValidation());
    }

    public function getHeureFinValidationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateFinValidation());
    }

    public function getDateDebutEvaluationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateDebutEvaluation());
    }

    public function getHeureDebutEvaluationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateFinEvaluation());
    }

    public function getDateFinEvaluationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatDate($stage->getDateFinEvaluation());
    }

    public function getHeureFinEvaluationStage(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->formatTime($stage->getDateFinEvaluation());
    }

    /** Sessions */
    public function getDateDebutSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateDebutStage());
    }

    public function getHeureDebutSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateDebutStage());
    }

    public function getDateFinSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateFinStage());
    }

    public function getHeureFinSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateFinStage());
    }

    public function getDateDebutChoixSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateDebutChoix());
    }

    public function getHeureDebutChoixSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateDebutChoix());
    }

    public function getDateFinChoixSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateFinChoix());
    }

    public function getHeureFinChoixSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateFinChoix());
    }

    public function getDateCommissionSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateCommission());
    }

    public function getHeureCommissionSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateCommission());
    }

    public function getDateFinCommissionSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateFinCommission());
    }

    public function getHeureFinCommissionSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateFinCommission());
    }

    public function getDateDebutValidationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateDebutValidation());
    }

    public function getHeureDebutValidationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateDebutValidation());
    }

    public function getDateFinValidationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateFinValidation());
    }

    public function getHeureFinValidationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateFinValidation());
    }

    public function getDateDebutEvaluationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateDebutEvaluation());
    }

    public function getHeureDebutEvaluationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateFinEvaluation());
    }

    public function getDateFinEvaluationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatDate($session->getDateFinEvaluation());
    }

    public function getHeureFinEvaluationSession(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if ($session === null) return null;
        return $this->formatTime($session->getDateFinEvaluation());
    }


    public function getDateCourante(): ?string
    {
        return $this->formatDate(new DateTime());
    }

    public function getHeureCourante(): ?string
    {
        return $this->formatTime(new DateTime());
    }

}