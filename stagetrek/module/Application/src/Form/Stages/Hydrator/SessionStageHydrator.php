<?php


namespace Application\Form\Stages\Hydrator;

use Application\Entity\Db\Groupe;
use Application\Entity\Db\SessionStage;
use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Provider\Parametre\ParametreProvider;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use DateInterval;
use DateTime;
use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorInterface;

/**
 * Class SessionStageHydrator
 * @package Application\Form\SessionsStages\Hydrator
 */
class SessionStageHydrator extends AbstractHydrator implements HydratorInterface
{
    use AnneeUniversitaireServiceAwareTrait;
    use GroupeServiceAwareTrait;
    use ParametreServiceAwareTrait;

    /**
     * @param object $object
     * @return array
     * @throws \DateMalformedIntervalStringException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function extract(object $object) : array
    {
        /** @var SessionStage $session */
        $session = $object;

        $defaultDates = $this->computeDefaultDate($session);
        return [
            SessionStageFieldset::ID => $session->getId(),
            SessionStageFieldset::LIBELLE => $session->getLibelle(),
            SessionStageFieldset::GROUPE => ($session->getGroupe()) ? $session->getGroupe()->getId() : null,
            SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS => ($session->getDateCalculOrdresAffectations()) ?
                $session->getDateCalculOrdresAffectations()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS],
            SessionStageFieldset::DATE_DEBUT_CHOIX => ($session->getDateDebutChoix()) ?
                $session->getDateDebutChoix()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_DEBUT_CHOIX],
            SessionStageFieldset::DATE_FIN_CHOIX => ($session->getDateFinChoix()) ?
                $session->getDateFinChoix()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_FIN_CHOIX],
            SessionStageFieldset::DATE_COMMISSION => ($session->getDateCommission()) ?
                $session->getDateCommission()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_COMMISSION],
            SessionStageFieldset::DATE_DEBUT_STAGE => ($session->getDateDebutStage()) ?
                $session->getDateDebutStage()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_DEBUT_STAGE],
            SessionStageFieldset::DATE_FIN_STAGE => ($session->getDateFinStage()) ?
                $session->getDateFinStage()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_FIN_STAGE],
            SessionStageFieldset::DATE_DEBUT_VALIDATION => ($session->getDateDebutValidation()) ?
                $session->getDateDebutValidation()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_DEBUT_VALIDATION],
            SessionStageFieldset::DATE_FIN_VALIDATION => ($session->getDateFinValidation()) ?
                $session->getDateFinValidation()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_FIN_VALIDATION],
            SessionStageFieldset::DATE_DEBUT_EVALUATION => ($session->getDateDebutEvaluation()) ?
                $session->getDateDebutEvaluation()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_DEBUT_EVALUATION],
            SessionStageFieldset::DATE_FIN_EVALUATION => ($session->getDateFinEvaluation()) ?
                $session->getDateFinEvaluation()->format('Y-m-d') : $defaultDates[SessionStageFieldset::DATE_FIN_EVALUATION],
            SessionStageFieldset::INPUT_SESSION_RATTRAPAGE => $session->isSessionRattrapge(),
        ];
    }

    /**
     * @param SessionStage $session
     * @return array
     * @throws \DateMalformedIntervalStringException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    protected function computeDefaultDate(SessionStage $session): array
    {
        $startDate = new DateTime();
        if ($session->getAnneeUniversitaire()) {
            $startDate = $session->getAnneeUniversitaire()->getDateDebut();
        }
        $groupe = $session->getGroupe();
        if($groupe){
            /** @var SessionStage $s */
            foreach ($groupe->getSessionsStages() as $s){
                if($startDate <  $s->getDateFinStage()){
                    $startDate = $s->getDateFinStage();
                }
            }
        }

        $defaultDates = [];

        $date = clone $startDate;
        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DATE_CALCUL_ORDRES_AFFECTATIONS);
        $date->sub(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS] = $date;

        $date = clone $startDate;
        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DATE_PHASE_CHOIX);
        $date->sub(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_DEBUT_CHOIX] = $date;

        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DUREE_PHASE_CHOIX);
        $date->add(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_FIN_CHOIX] = $date;

        $date = clone $startDate;
        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DATE_PHASE_AFFECTATION);
        $date->sub(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_COMMISSION] = $date;

        $date = clone $startDate;
        $defaultDates[SessionStageFieldset::DATE_DEBUT_STAGE] = $date;
        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DUREE_STAGE);
        $date->sub(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_FIN_STAGE] = $date;
        $endDate = $date;

        $date = clone $endDate;
        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DATE_PHASE_VALIDATION);
        $date->add(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_DEBUT_VALIDATION] = $date;

        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DUREE_PHASE_VALIDATION);
        $date->add(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_FIN_VALIDATION] = $date;

        $date = clone $endDate;
        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DATES_PHASE_EVALUATION);
        $date->add(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_DEBUT_EVALUATION] = $date;

        $delta = $this->getParametreService()->getParametreValue(ParametreProvider::DUREE_PHASE_VALIDATION);
        $date->add(new DateInterval('P' . $delta . 'D'));
        $defaultDates[SessionStageFieldset::DATE_FIN_EVALUATION] = $date;

        return $defaultDates;
    }

    /**
     * Hydrate $object with the provided $data.
     * @param array $data
     * @param $object
     * @return SessionStage
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function hydrate(array $data, $object): SessionStage
    {
        /** @var SessionStage $session */
        $session = $object;

        if(isset($data[SessionStageFieldset::LIBELLE])){
            $session->setLibelle(trim($data[SessionStageFieldset::LIBELLE]));
        }
        if (isset($data[SessionStageFieldset::GROUPE])) {
            /** @var Groupe $groupe */
            $groupe = $this->getGroupeService()->find($data[SessionStageFieldset::GROUPE]);
            if($groupe){
                $session->setGroupe($groupe);
                $session->setAnneeUniversitaire($groupe->getAnneeUniversitaire());
            }
            else{//permet de forcer des erreurs a la suite
                $session->setGroupe(null);
                $session->setAnneeUniversitaire(null);
            }
        }
        if ($data[SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if(!$session->getDateCalculOrdresAffectations()
                || $session->getDateCalculOrdresAffectations()->getTimestamp() != $date->getTimestamp()){
                $session->setDateCalculOrdresAffectations($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_DEBUT_CHOIX]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_DEBUT_CHOIX]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if(!$session->getDateDebutChoix()
                || $session->getDateDebutChoix()->getTimestamp() != $date->getTimestamp()){
                $session->setDateDebutChoix($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_FIN_CHOIX]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_FIN_CHOIX]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(23, 59);
            if(!$session->getDateFinChoix()
                || $session->getDateFinChoix()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateFinChoix($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_COMMISSION]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_COMMISSION]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if(!$session->getDateCommission()
                || $session->getDateCommission()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateCommission($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_DEBUT_STAGE]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_DEBUT_STAGE]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if(!$session->getDateDebutStage()
                || $session->getDateDebutStage()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateDebutStage($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_FIN_STAGE]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_FIN_STAGE]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(23, 59);
            if(!$session->getDateFinStage()
                || $session->getDateFinStage()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateFinStage($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_DEBUT_VALIDATION]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_DEBUT_VALIDATION]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if(!$session->getDateDebutValidation()
                || $session->getDateDebutValidation()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateDebutValidation($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_FIN_VALIDATION]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_FIN_VALIDATION]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(23, 59);
            if(!$session->getDateFinValidation()
                || $session->getDateFinValidation()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateFinValidation($date);
            }
        }

        if ($data[SessionStageFieldset::DATE_DEBUT_EVALUATION]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_DEBUT_EVALUATION]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(0, 0);
            if(!$session->getDateDebutEvaluation()
                || $session->getDateDebutEvaluation()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateDebutEvaluation($date);
            }
        }
        if ($data[SessionStageFieldset::DATE_FIN_EVALUATION]) {
            $date = new DateTime();
            $chaine = explode("-", $data[SessionStageFieldset::DATE_FIN_EVALUATION]);
            $date->setDate($chaine[0], $chaine[1], $chaine[2]);
            $date->setTime(23, 59);
            if(!$session->getDateFinEvaluation()
                || $session->getDateFinEvaluation()->getTimestamp() != $date->getTimestamp()) {
                $session->setDateFinEvaluation($date);
            }
        }
        $rattrapage = boolval(($data[SessionStageFieldset::INPUT_SESSION_RATTRAPAGE]) ?? 0);
        $session->setSessionRattrapage($rattrapage);
        return $session;
    }

}