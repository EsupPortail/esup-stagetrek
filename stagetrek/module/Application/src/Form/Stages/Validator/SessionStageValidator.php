<?php

namespace Application\Form\Stages\Validator;

use Application\Entity\Db\Groupe;
use Application\Entity\Db\SessionStage;
use Application\Form\Stages\Fieldset\SessionStageFieldset;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Validator\AbstractValidator;

/**
 * Class SessionStagValidator
 * @package Application\Form\Validator
 */
class SessionStageValidator extends AbstractValidator implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use SessionStageServiceAwareTrait;

    //Fonction de callback
    const ASSERT_LIBELLE = "assertLibelle";
    const ASSERT_GROUPE = "assertGroupe";
    const ASSERT_DATE_CALCUL_ORDRES_AFFECTATIONS = "assertDateCalculOrdreAffectations";
    const ASSERT_DATE_DEBUT_CHOIX = "assertDateDebutChoix";
    const ASSERT_DATE_FIN_CHOIX = "assertDateFinChoix";
    const ASSERT_DATE_COMMISSION = "assertDateCommission";
    const ASSERT_DATE_DEBUT_STAGE = "assertDateDebutStage";
    const ASSERT_DATE_FIN_STAGE = "assertDateFinStage";
    const ASSERT_DATE_DEBUT_VALIDATION = "assertDateDebutValidation";
    const ASSERT_DATE_FIN_VALIDATION = "assertDateFinValidation";
    const ASSERT_DATE_DEBUT_EVALUATION = "assertDateDebutEvaluation";
    const ASSERT_DATE_FIN_EVALUTATION = "assertDateFinEvaluation";

    const CALLBACK_FUNCTION_NOT_DEFIND_ERROR = 'CALLBACK_FUNCTION_NOT_DEFIND_ERROR';
    const CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR = 'CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR';
    const LIBELLE_ALREADY_USED_ERROR = 'ACRONYME_ALREADY_USED_ERROR';
    const GROUPE_NOT_FOUND_ERROR = 'GROUPE_NOT_FOUND_ERROR';
    const GROUPE_CANT_BE_REDEFIND = 'GROUPE_CANT_BE_REDEFIND';
    const NOT_A_DATE_ERROR = 'NOT_A_DATE_ERROR';
    const GROUPE_ALREADY_IN_STAGE = 'GROUPE_ALREADY_IN_STAGE';
    const DATE_PRECENDANTE_ORDER_ERROR = 'DATE_PRECENDANTE_ORDER_ERROR';
    const DATE_SUIVANTE_ORDER_ERROR = 'DATE_SUIVANTE_ORDER_ERROR';
    const ANNEE_VALIDEE_ERROR = "ANNEE_VALIDEE_ERROR";
    const DATE_ANNEE_VALIDEE_ERROR = "DATE_ANNEE_VALIDEE_ERROR";

    /**
     * Validation failure message templates definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR => "La fonction de validation n'a pas été founrie.",
        self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR => "La fonction de validation n'a pas été définie.",
        self::LIBELLE_ALREADY_USED_ERROR => "Ce libellé de session est déjà utilisé pour le groupe %groupeLibelle%",
        self::GROUPE_NOT_FOUND_ERROR => "Le groupe demandé n'a pas été trouvé.",
        self::GROUPE_CANT_BE_REDEFIND => "Le groupe de la session de stage ne peux pas être modifié",
        self::GROUPE_ALREADY_IN_STAGE => "Le groupe %groupeLibelle% a déjà un stage entre les dates indiquées",
        self::ANNEE_VALIDEE_ERROR => "Impossible d'ajouter une nouvelle session de stage car l'année universitaire %anneeLibelle% est validée",
        self::NOT_A_DATE_ERROR => "La date de %date1Libelle% n'est pas valide",
        self::DATE_PRECENDANTE_ORDER_ERROR => "La date de %date1Libelle% doit précéder la date de %date2Libelle%",
        self::DATE_SUIVANTE_ORDER_ERROR => "La date de %date1Libelle% doit précéder la date de %date2Libelle%",
        self::DATE_ANNEE_VALIDEE_ERROR => "La date de %date1Libelle% ne peux pas être modifier car l'année universitaire %anneeLibelle% est validée",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'groupeLibelle' => 'groupeLibelle',
        'anneeLibelle' => 'anneeLibelle',
        'date1Libelle' => 'date1Libelle',
        'date2Libelle' => 'date2Libelle',
    ];
    protected string $groupeLibelle = "";
    protected string $anneeLibelle = "";
    protected string $date1Libelle = "";
    protected string $date2Libelle = "";

    protected array $datesLibelles=[
        SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS => "calcul des ordres d'affectations",
        SessionStageFieldset::DATE_DEBUT_CHOIX => "début des choix",
        SessionStageFieldset::DATE_FIN_CHOIX => "fin des choix",
        SessionStageFieldset::DATE_COMMISSION => "la commission",
        SessionStageFieldset::DATE_DEBUT_STAGE => "début des stage",
        SessionStageFieldset::DATE_FIN_STAGE => "fin des stage",
        SessionStageFieldset::DATE_DEBUT_VALIDATION => "début des validations",
        SessionStageFieldset::DATE_FIN_VALIDATION => "fin des validations",
        SessionStageFieldset::DATE_DEBUT_EVALUATION => "début des évaluations",
        SessionStageFieldset::DATE_FIN_EVALUATION => "fin des évaluations",
    ];

    /**
     * Constructor
     *
     * @param array|callable $options
     */
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = ['callback' => $options];
        }

        parent::__construct($options);
    }

    /**
     * Validation
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, array $context = []): bool
    {
        if(!key_exists('callback', $this->getOptions())){
            $this->error(self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR);
            return false;
        }
        $callback = $this->getOption('callback');
        if(!method_exists(self::class, $callback)){
            $this->error(self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR);
            return false;
        }
        return $this->$callback($value, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function assertLibelle(mixed $value, array $context =[]): bool
    {
        $s1 = $this->getSessionStage($context);
        $groupe = $this->getGroupe($context);
        if(!$groupe){return true;} //Invalide mais pas du fait du libellé
        /** @var SessionStage $session */
        $session = $this->getSessionStageService()->findOneBy([
            "libelle" => $value,
            "groupe" => $groupe
        ]);
        if((isset($session) && !isset($s1)) || (isset($session) && $s1->getId() !=$session->getId())){
            $this->groupeLibelle = $groupe->getLibelle();
            $this->error(self::LIBELLE_ALREADY_USED_ERROR);
            return false;
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \DateMalformedStringException
     */
    public function assertGroupe(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $groupe = $this->getGroupe($context);
        if(!$groupe){
            $this->error(self::GROUPE_NOT_FOUND_ERROR);
            return false;
        }
        $session = $this->getSessionStage($context);
        if(isset($session) && $session->getGroupe()->getId() != $groupe->getId()){
            $this->error(self::GROUPE_CANT_BE_REDEFIND);
            return false;
        }
        else if(!isset($session) && $groupe->getAnneeUniversitaire()->isAnneeVerrouillee()){
            $this->error(self::ANNEE_VALIDEE_ERROR);
            return false;
        }
        //Est-ce que le groupe est déjà en stage
        if(!$this->assertGroupeAlreadyInStage($context)){
            return false;
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateCalculOrdreAffectations(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = null;
        $datePrincipalId = SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS;
        $dateSuivanteId = SessionStageFieldset::DATE_DEBUT_CHOIX;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }


    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateDebutChoix(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = SessionStageFieldset::DATE_CALCUL_ORDRES_AFFECTACTIONS;
        $datePrincipalId = SessionStageFieldset::DATE_DEBUT_CHOIX;
        $dateSuivanteId = SessionStageFieldset::DATE_FIN_CHOIX;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateFinChoix(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = SessionStageFieldset::DATE_DEBUT_CHOIX;
        $datePrincipalId = SessionStageFieldset::DATE_FIN_CHOIX;
        $dateSuivanteId = SessionStageFieldset::DATE_COMMISSION;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateCommission(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = SessionStageFieldset::DATE_FIN_CHOIX;
        $datePrincipalId = SessionStageFieldset::DATE_COMMISSION;
        $dateSuivanteId = SessionStageFieldset::DATE_DEBUT_STAGE;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function assertDateDebutStage(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $groupe = $this->getGroupe($context);
        if($groupe && $groupe->getAnneeUniversitaire()->isAnneeVerrouillee()){
            $session = $this->getSessionStage($context);
            $date = (isset($context[SessionStageFieldset::DATE_DEBUT_STAGE])) ?
                new DateTime($context[SessionStageFieldset::DATE_DEBUT_STAGE]) : new DateTime();
            if($session && $session->getDateDebutStage()->getTimestamp() != $date->getTimestamp()){
                $this->anneeLibelle = $groupe->getAnneeUniversitaire()->getLibelle();
                $this->date1Libelle = $this->datesLibelles[SessionStageFieldset::DATE_DEBUT_STAGE];
                $this->error(self::DATE_ANNEE_VALIDEE_ERROR);
                return false;
            }
        }
        elseif ($groupe){ //Est-ce que le groupe est déjà en stage
            if(!$this->assertGroupeAlreadyInStage($context)){
                return false;
            }
        }

        $datePrecedenteId = SessionStageFieldset::DATE_COMMISSION;
        $datePrincipalId = SessionStageFieldset::DATE_DEBUT_STAGE;
        $dateSuivanteId = SessionStageFieldset::DATE_FIN_STAGE;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function assertDateFinStage(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $groupeId = (isset($context[SessionStageFieldset::GROUPE]) && $context[SessionStageFieldset::GROUPE] != '') ?
            intval($context[SessionStageFieldset::GROUPE]) : 0;
        /** @var Groupe $groupe */
        $groupe = ($groupeId!=0) ? $this->getObjectManager()->getRepository(Groupe::class)->find($groupeId) : null;
        /** @var DateTime $date */
        if($groupe && $groupe->getAnneeUniversitaire()->isAnneeVerrouillee()){
            $session = $this->getSessionStage($context);
            $date = (isset($context[SessionStageFieldset::DATE_FIN_STAGE])) ?
                new DateTime($context[SessionStageFieldset::DATE_FIN_STAGE]) : new DateTime();
            $date->setTime(23, 59);
            if($session && $session->getDateFinStage()->getTimestamp() != $date->getTimestamp()){
                $this->anneeLibelle = $groupe->getAnneeUniversitaire()->getLibelle();
                $this->date1Libelle = $this->datesLibelles[SessionStageFieldset::DATE_FIN_STAGE];
                $this->error(self::DATE_ANNEE_VALIDEE_ERROR);
                return false;
            }
        }
        elseif ($groupe){ //Est-ce que le groupe est déjà en stage
            if(!$this->assertGroupeAlreadyInStage($context)){
                return false;
            }
        }

        $datePrecedenteId = SessionStageFieldset::DATE_DEBUT_STAGE;
        $datePrincipalId = SessionStageFieldset::DATE_FIN_STAGE;
        $dateSuivanteId = SessionStageFieldset::DATE_DEBUT_EVALUATION;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateDebutValidation(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = SessionStageFieldset::DATE_DEBUT_STAGE;
        $datePrincipalId = SessionStageFieldset::DATE_DEBUT_VALIDATION;
        $dateSuivanteId = SessionStageFieldset::DATE_FIN_VALIDATION;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateFinValidation(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = SessionStageFieldset::DATE_DEBUT_VALIDATION;
        $datePrincipalId = SessionStageFieldset::DATE_FIN_VALIDATION;
        $dateSuivanteId = SessionStageFieldset::DATE_FIN_EVALUATION;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateDebutEvaluation(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $datePrecedenteId = SessionStageFieldset::DATE_FIN_STAGE;
        $datePrincipalId = SessionStageFieldset::DATE_DEBUT_EVALUATION;
        $dateSuivanteId = SessionStageFieldset::DATE_FIN_EVALUATION;
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateFinEvaluation(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        //Test entre la date de début des évaluation et la de fin
        $datePrecedenteId = SessionStageFieldset::DATE_DEBUT_EVALUATION;
        $datePrincipalId = SessionStageFieldset::DATE_FIN_EVALUATION;
        $dateSuivanteId = null;
        if(!$this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context)){
            return false;
        }
        return $this->assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, $context);
    }

    /**
     * si datePrecendenteId ou dateSuivanteId sont null, on ne cherche pas la comparaison
     * @throws \DateMalformedStringException
     */
    private function assertDates($datePrincipalId, $datePrecedenteId, $dateSuivanteId, array $context): bool
    {
        //On vérifier que les dates existes
        $datePrincipal=(isset($context[$datePrincipalId]) && $context[$datePrincipalId] != '') ?
            new DateTime($context[$datePrincipalId]) : null;
        if(!$datePrincipal){
            $this->date1Libelle=$this->datesLibelles[$datePrincipalId];
            $this->error(self::NOT_A_DATE_ERROR);
            return false;
        }
        //Faux mais a cause des dates précédentes/suivantes et donc pas sur la date courante
        if($datePrecedenteId !==null){
            $datePrecedente=(isset($context[$datePrecedenteId]) && $context[$datePrecedenteId] != '') ?
                new DateTime($context[$datePrecedenteId]) : null;
            if(!$datePrecedente){
                $this->date1Libelle=$this->datesLibelles[$datePrecedenteId];
//                $this->error(self::NOT_A_DATE_ERROR);
                return false;
            }
        }
        if($dateSuivanteId !==null){
            $dateSuivante=(isset($context[$dateSuivanteId]) && $context[$dateSuivanteId] != '') ?
                new DateTime($context[$dateSuivanteId]) : null;
            if(!$dateSuivante){
                $this->date1Libelle=$this->datesLibelles[$dateSuivanteId];
//                $this->error(self::NOT_A_DATE_ERROR);
                return false;
            }
        }

        //On vérifier que les ordres
        $noError = true;
        if(isset($datePrecedente) && $datePrincipal<=$datePrecedente){
            $this->date1Libelle=$this->datesLibelles[$datePrecedenteId];
            $this->date2Libelle=$this->datesLibelles[$datePrincipalId];
            $this->error(self::DATE_PRECENDANTE_ORDER_ERROR);
            $noError=false;
        }
        if(isset($dateSuivante) && $dateSuivante<=$datePrincipal){
            $this->date1Libelle=$this->datesLibelles[$datePrincipalId];
            $this->date2Libelle=$this->datesLibelles[$dateSuivanteId];
            $this->error(self::DATE_SUIVANTE_ORDER_ERROR);
            $noError=false;
        }
        return $noError;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    private function assertGroupeAlreadyInStage(array $context) : bool
    {
        $sessionId  = (isset($context[SessionStageFieldset::ID]) && $context[SessionStageFieldset::ID] != '') ?
            intval($context[SessionStageFieldset::ID]) : 0;
        $groupe = $this->getGroupe($context);
        if(!$groupe){return true;}//Faux mais pas du au date

        $dateDebut = (isset($context[SessionStageFieldset::DATE_DEBUT_STAGE])) ?
            new DateTime($context[SessionStageFieldset::DATE_DEBUT_STAGE]) : new DateTime();
        /** @var DateTime $dateDebut */
        $dateFin = (isset($context[SessionStageFieldset::DATE_FIN_STAGE])) ?
            new DateTime($context[SessionStageFieldset::DATE_FIN_STAGE]) : new DateTime();

        $sessions = $this->getSessionStageService()->findByGroupe($groupe);
        foreach ($sessions as $s){
            if($s->getId()==$sessionId){continue;}
            if( ($s->getDateDebutStage() <= $dateDebut && $dateDebut <= $s->getDateFinStage())
                || ($s->getDateDebutStage() <= $dateFin && $dateFin <= $s->getDateFinStage())
                || ($dateDebut <= $s->getDateDebutStage() && $s->getDateDebutStage() <= $dateFin)
                || ($dateDebut <= $s->getDateFinStage() && $s->getDateFinStage() <= $dateFin)
            ){
                $this->groupeLibelle = $groupe->getLibelle();
                $this->error(self::GROUPE_ALREADY_IN_STAGE);
                return false;
            }
        }
        return true;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    protected function getSessionStage(array $context) : ?SessionStage
    {
        $sessionId = (isset($context[SessionStageFieldset::ID]) && $context[SessionStageFieldset::ID] != '') ?
            intval($context[SessionStageFieldset::ID]) : 0;
        return $this->getSessionStageService()->find($sessionId);
    }

    protected function getGroupe(array $context) : ?Groupe
    {
        $groupeId=  (isset($context[SessionStageFieldset::GROUPE]) && $context[SessionStageFieldset::GROUPE] != '') ?
            intval($context[SessionStageFieldset::GROUPE]) : 0;
        return $this->getObjectManager()->getRepository(Groupe::class)->find($groupeId);
    }
}