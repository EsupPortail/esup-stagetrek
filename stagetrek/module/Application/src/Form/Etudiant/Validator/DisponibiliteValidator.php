<?php

namespace Application\Form\Etudiant\Validator;

use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Form\Etudiant\Fieldset\DisponibiliteFieldset;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Validator\AbstractValidator;

/**
 * Class DisponibiliteValidator
 * @package Application\Form\Disponibilite
 */
class DisponibiliteValidator extends AbstractValidator implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    //Fonction de callback
    const ASSERT_ETUDIANT = "assertEtudiant";
    const ASSERT_DATE_DEBUT = "assertDateDebut";
    const ASSERT_DATE_FIN = "assertDateFin";

    const CALLBACK_FUNCTION_NOT_DEFIND_ERROR = 'CALLBACK_FUNCTION_NOT_DEFIND_ERROR';
    const CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR = 'CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR';

    const ETUDIANT_HAS_SESSION_WARNING = 'ETUDIANT_HAS_SESSION_WARNING';
//    const ETUDIANT_HAS_AFFECTATION_ERROR = 'ETUDIANT_HAS_AFFECTATION_ERROR';
    const ETUDIANT_NOT_FOUND_ERROR = 'ETUDIANT_NOT_FOUND_ERROR';
    const INVALIDE_DATE_DEBUT_ERROR = 'INVALIDE_DATE_DEBUT_ERROR';
    const INVALIDE_DATE_FIN_ERROR = 'INVALIDE_DATE_FIN_ERROR';
    const DATE_ORDER_ERROR = 'DATE_ORDER_ERROR';
    const DISPO_ALREADY_EXIST_ERROR = "DISPO_ALREADY_EXIST_ERROR";

    /**
     * Validation failure message templates definitions
     * @var array
     */
    protected $messageTemplates = [
        self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR => "La fonction de validation n'a pas été founrie.",
        self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR => "La fonction de validation n'a pas été définie.",
        self::ETUDIANT_NOT_FOUND_ERROR => "L'étudiant.e associé.e à la disponibilité n'a pas été trouvé.e",
        self::INVALIDE_DATE_DEBUT_ERROR => "La date de début n'est pas valide",
        self::INVALIDE_DATE_FIN_ERROR => "La date de fin n'est pas valide",
        self::DATE_ORDER_ERROR => "La date de début doit précéder la date de fin",
        self::DISPO_ALREADY_EXIST_ERROR => "%etudiantDisplayName% a déjà une disponibilité dans la période demandée.",
//        self::ETUDIANT_HAS_AFFECTATION_ERROR => "%etudiantDisplayName% a au moins une affectation de stage dans la période demandée.",
        self::ETUDIANT_HAS_SESSION_WARNING => "%etudiantDisplayName% est inscrit pour des stages sur la période demandé. Coché la case 'Forcer la disponibilité' pour appliquer sa mise en disposition.",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'etudiantDisplayName' => 'etudiantDisplayName',
    ];
    protected string $etudiantDisplayName = "";

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
     * @throws \DateMalformedStringException
     */
    public function assertEtudiant(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $etudiantId = (isset($context[DisponibiliteFieldset::ETUDIANT]) && $context[DisponibiliteFieldset::ETUDIANT] != '') ?
            intval($context[DisponibiliteFieldset::ETUDIANT]) : 0;
        /** @var Etudiant $etudiant */
        $etudiant = ($etudiantId!=0) ? $this->getObjectManager()->getRepository(Etudiant::class)->find($etudiantId) : null;
        if(!$etudiant){
            $this->error(self::ETUDIANT_NOT_FOUND_ERROR);
            return false;
        }
        $dateDebut=(isset($context[DisponibiliteFieldset::DATE_DEBUT]) && $context[DisponibiliteFieldset::DATE_DEBUT] != '') ?
            new DateTime($context[DisponibiliteFieldset::DATE_DEBUT]) : null;
        $dateFin=(isset($context[DisponibiliteFieldset::DATE_FIN]) && $context[DisponibiliteFieldset::DATE_FIN] != '') ?
            new DateTime($context[DisponibiliteFieldset::DATE_FIN]) : null;
        if(!$dateDebut||!$dateFin){return true;} //Faux mais pas a cause de l'étudiant


        $modeForce= boolval($context[DisponibiliteFieldset::FORCER_DISPONIBILITE]);
        if($modeForce) return true;
        /** @var SessionStage $session */
        foreach ($etudiant->getSessionsStages() as $session){
            if($session->getDateFinStage()<$dateDebut){
                continue;
            }
            if($dateFin < $session->getDateDebutStage()){
                continue;
            }

            $this->etudiantDisplayName=$etudiant->getDisplayName();
            $this->error(self::ETUDIANT_HAS_SESSION_WARNING);
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
    public function assertDateDebut(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $dispoId = (isset($context[DisponibiliteFieldset::ID]) && $context[DisponibiliteFieldset::ID] != '') ?
            intval($context[DisponibiliteFieldset::ID]) : null;
        /** @var Disponibilite $dispo */
        $dispo = ($dispoId!=0) ? $this->getObjectManager()->getRepository(Disponibilite::class)->find($dispoId) : null;

        $etudiantId = (isset($context[DisponibiliteFieldset::ETUDIANT]) && $context[DisponibiliteFieldset::ETUDIANT] != '') ?
            intval($context[DisponibiliteFieldset::ETUDIANT]) : 0;
        /** @var Etudiant $etudiant */
        $etudiant = ($etudiantId!=0) ? $this->getObjectManager()->getRepository(Etudiant::class)->find($etudiantId) : null;
        if(!$etudiant){return true;} //Invalide mais pas du fait des dates

        //On vérifier que les dates existes
        $dateDebut=(isset($context[DisponibiliteFieldset::DATE_DEBUT]) && $context[DisponibiliteFieldset::DATE_DEBUT] != '') ?
            new DateTime($context[DisponibiliteFieldset::DATE_DEBUT]) : null;
        if(!$dateDebut){
            $this->error(self::INVALIDE_DATE_DEBUT_ERROR);
            return false;
        }
        $dateFin=(isset($context[DisponibiliteFieldset::DATE_FIN]) && $context[DisponibiliteFieldset::DATE_FIN] != '') ?
            new DateTime($context[DisponibiliteFieldset::DATE_FIN]) : null;
        if(!$dateFin){//Faux mais pas a cause de la date de début
            return true;
        }
        if($dateDebut>=$dateFin){
            $this->error(self::DATE_ORDER_ERROR);
            return false;
        }
        return $this->assertNoDisponibiliteDateOverlap($dispo, $etudiant, $dateDebut, $dateFin);
    }


    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     * @throws \DateMalformedStringException
     */
    public function assertDateFin(mixed $value, array $context =[]): bool
    {
        if(!isset($value)){return false;}
        $dispoId = (isset($context[DisponibiliteFieldset::ID]) && $context[DisponibiliteFieldset::ID] != '') ?
            intval($context[DisponibiliteFieldset::ID]) : null;
        /** @var Disponibilite $dispo */
        $dispo = ($dispoId!=0) ? $this->getObjectManager()->getRepository(Disponibilite::class)->find($dispoId) : null;

        $etudiantId = (isset($context[DisponibiliteFieldset::ETUDIANT]) && $context[DisponibiliteFieldset::ETUDIANT] != '') ?
            intval($context[DisponibiliteFieldset::ETUDIANT]) : null;
        /** @var Etudiant $etudiant */
        $etudiant = ($etudiantId!=0) ? $this->getObjectManager()->getRepository(Etudiant::class)->find($etudiantId) : null;
        if(!$etudiant){return false;} //Invalide mais pas du fait des dates

        //On vérifier que les dates existes
        $dateDebut=(isset($context[DisponibiliteFieldset::DATE_DEBUT]) && $context[DisponibiliteFieldset::DATE_DEBUT] != '') ?
            new DateTime($context[DisponibiliteFieldset::DATE_DEBUT]) : null;
        if(!$dateDebut){//Faux mais pas a cause de la date de fin
            return true;
        }
        $dateFin=(isset($context[DisponibiliteFieldset::DATE_FIN]) && $context[DisponibiliteFieldset::DATE_FIN] != '') ?
            new DateTime($context[DisponibiliteFieldset::DATE_FIN]) : null;
        if(!$dateFin){
            $this->error(self::INVALIDE_DATE_FIN_ERROR);
            return false;
        }
        if($dateDebut>=$dateFin){
            $this->error(self::DATE_ORDER_ERROR);
            return false;
        }
        return $this->assertNoDisponibiliteDateOverlap($dispo, $etudiant, $dateDebut, $dateFin);
    }

    //Vérifie qu'il n'y as pas de chevauchement entre les dates disponibilité de l'étudiant et la disponibilité crée/modifié
    public function assertNoDisponibiliteDateOverlap($dispo, Etudiant $etudiant, DateTime $dateDebut, DateTime $dateFin): bool
    {
        $dispoId = (isset($dispo)) ? $dispo->getId() : 0;
        /** @var Disponibilite $otherDispo */
        foreach($etudiant->getDisponibilites() as $otherDispo){
            if($otherDispo->getId() == $dispoId) continue; //Pour ne pas comparer la disponibilité que l'on veut modifier avec elle même
            if($otherDispo->getDateFin()<$dateDebut){
                continue;
            }
            if($dateFin < $otherDispo->getDateDebut()){
                continue;
            }
            $this->etudiantDisplayName=$etudiant->getDisplayName();
            $this->error(self::DISPO_ALREADY_EXIST_ERROR);
            return false;
        }
        return true;

    }
}