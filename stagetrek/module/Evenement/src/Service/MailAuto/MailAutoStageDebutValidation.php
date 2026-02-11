<?php
namespace Evenement\Service\MailAuto;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Service\Contact\ContactStageService;
use Application\Service\Mail\MailService;
use DateInterval;
use DateTime;
use Evenement\Provider\EvenementEtatProvider;
use Evenement\Provider\TypeEvenementProvider;
use Evenement\Service\MailAuto\Abstract\AbstractMailAutoEvenementService;
use Exception;
use Laminas\Json\Json;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnicaenEvenement\Entity\Db\Evenement;
use Application\Entity\Db\Contact;
use UnicaenMail\Entity\Db\Mail;

class MailAutoStageDebutValidation extends AbstractMailAutoEvenementService
{
    /**
     * @return SessionStage[]
     * @throws Exception
     * @desc retourne la liste des sessions de stages requiérant se mail
     */
    public function findEntitiesForNewEvent(): array
    {
        $parametrePlanification = $this->getParametreService()->getParametreValue(Parametre::DATE_PLANIFICATIONS_MAILS);
        //Sessions dont les dates sont proches du début de la phase de validation
        $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();

        $sessions = array_filter($sessions, function (SessionStage $session) use($parametrePlanification){
            if($session->hasEtatAlerte() || $session->hasEtatError()){return false;}
            $date1 = clone($session->getDateDebutValidation());
            $date1->sub(new DateInterval('P' . $parametrePlanification . 'D'));
            $date2 = clone($session->getDateFinValidation());
            $date1->setTime(0,0);
            $date2->setTime(23,59);
            $today = new DateTime();
            if(!($date1 <= $today && $today <=$date2)){return false;}
            //On regarde si l'événement a déjà été généré
            $params = [
                    'type_code' => TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
                    'session_id' => $session->getId(),
                ];
                // Vérification que l'événement n'existe pas déjà
                $events = $this->findEvenementsWithParametres($params);
            return !isset($events) || empty($events);
        });

        return $sessions;
//          todo : gerer dans l'envoie du mails la génération ou non du liens de validation du stages
//        /** @var Stage[] $stages */
//        $stages = [];
//        $today = new DateTime();
//        /** @var SessionStage $session */
//        foreach ($sessions as $session){
//            /** @var Stage $stage */
//            foreach ($session->getStages() as $stage){
//                if($stage->isNonEffectue()){continue;}
//                if($stage->getValidationStage()->isValide()){continue;}
//                if($stage->hasEtatEnErreur() || $stage->hasEtatEnAlerte()){continue;}
//                if(!$stage->getAffectationStage() || !$stage->getAffectationStage()->hasEtatValidee()){
//                    continue;
//                }
//                if ($stage->getDateFinValidation() < $today) { continue;}
//                $stages[]=$stage;
//            }
//        }
//        /** @var ContactStage[] $contacts */
//        $contacts=[];
//        foreach ($stages as $stage) {
//            /** @var ContactStage $contact */
//            foreach ($stage->getContactsStages() as $contact) {
//                if (!$contact->canValiderStage()) {
//                    continue;
//                }
//                if (!$contact->isActif()) {
//                    continue;
//                }
//                if (!$contact->getEmail()) {
//                    continue;
//                }
//                if (!filter_var($contact->getEmail(), FILTER_VALIDATE_EMAIL)) {
//                    continue;
//                }
//                $stage=$contact->getStage();
//                $session=$stage->getSessionStage();
//                $params = [
//                    'type_code' => TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
//                    'session_id' => $session->getId(),
//                    'stage_id' => $stage->getId(),
//                    'contact_stage_id' => $contact->getId(),
//                ];
//                // Vérification que l'événement n'existe pas déjà
//                $events = $this->findEvenementsWithParametres($params);
//                if(!empty($events)){
//                    continue;}
//                $contacts[] = $contact;
//            }
//        }
//        return $contacts;
    }

    /**
     * @param SessionStage $session
     * @return Evenement
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function create(mixed $session): Evenement
    {
        if(!$session instanceof SessionStage){
            throw new Exception("Les événements de type ".TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE." attendent une session de stage en entrée.");
        }
        $evenementType = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE);

        $eventName = sprintf("%s - Session #%s", $evenementType->getLibelle(), $session->getId());
        $description = sprintf("%s - Session %s", $evenementType->getDescription(), $session->getLibelle());

        $today =  new DateTime();
        //Calcul d'une date de traitement adaptès selon la situation
        $datePlanification = $session->getDateDebutValidation();
        $datePlanification->setTime(8, 0);
        if($datePlanification < $today){
            $datePlanification = $today;
        }

        $parametres['session_id'] =  "".$session->getId();
        /** On regarde s'il n'y en a pas déjà un en attente */
        $params = [
            'type_code' => TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
            'session_id' => "".$session->getId(),
            'etat_code' => EvenementEtatProvider::EN_ATTENTE,
        ];
        // Vérification que l'événement n'existe pas déjà
        $events = $this->findEvenementsWithParametres($params);
        if(!empty($events)){
            return current($events);
        }

        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $evenementType, $parametres, $datePlanification);
        $this->ajouter($evenement);
        return $evenement;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \DateMalformedIntervalStringException
     * @throws \Exception
     */
    public function traiter(Evenement $evenement): string
    {
//        if($evenement->getEtat()->getCode() != EvenementEtatProvider::EN_ATTENTE){
//            return $evenement->getEtat()->getCode();
//        }
//        $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::EN_COURS));
        //Rechercher les datas nessaires pour l'envoie du mail
        $parametres = Json::decode($evenement->getParametres(), Json::TYPE_ARRAY);
        $sessionId = ($parametres['session_id']) ?? 0;
        $session = $this->getObjectManager()->getRepository(SessionStage::class)->find($sessionId);
        if (!$session) {
            $evenement->setLog("La session de stage n'as pas été trouvée");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        if($session->getDateFinValidation() < new DateTime()) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail automatique après la date de fin de la phase de validation");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        $contacts = $this->preparerContacts($session);
        if(empty($contacts)){
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog("Aucun contacts ne peux recevoir le mail de validation");
            $evenement->setDateTraitement(new DateTime());
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        $logs = "";
        $cptSuccess = 0;
        $cptFailure = 0;
        $cptIgnore = 0;
        /** @var  $contact */
        foreach ($contacts as $contact) {
            try {
                if($this->isMailAlreadySend($contact, $session)){
                    $cptIgnore++;
                    continue;
                }
                $mailData = ['contact' => $contact, 'session' => $session];
                //on regarde si le mails n'as pas déjà été envoyé

                $mail = $mailService->sendMailType(CodesMailsProvider::MAIL_AUTO_VALIDATIONS_STAGES, $mailData);
                if ($mail->getStatusEnvoi() == Mail::SUCCESS) {
                    $cptSuccess++;
                } else {
                    throw new Exception($mail->getLog());
                }
            } catch (Exception $e) {
                $logs .= sprintf("\n Echec de l'envoie du mail à %s : %s", $contact->getEmail(), $e->getMessage());
                $cptFailure++;
                if ($cptFailure > 10) {
                    break; //trop d'echec = on arrête
                }
            }
        }
        $evenement->setDateTraitement(new DateTime());
        $logs .= sprintf("\n%s mails envoyés", $cptSuccess);
        if($cptIgnore > 0){
            $logs .= sprintf("\n%s cas ignorés car le mail a déjà été envoyée", $cptIgnore);
        }
        if($cptFailure > 0){
            $logs .= sprintf("\n%s mails ont échoué", $cptFailure);
        }
        $evenement->setLog($logs);
        $this->changerEtat($evenement, ($cptFailure == 0) ? $this->getEventEtat(EvenementEtatProvider::SUCCES) : $this->getEventEtat(EvenementEtatProvider::ECHEC));
        return $evenement->getEtat()->getCode();
//        if (!$contact) {
//            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
//            return $evenement->getEtat()->getCode();
//        }
//        //Quelques vérification avant pour éviter des erreurs
//        $today = new DateTime();
//
//        //Génération d'un token de validation si le stage n'en as pas déjà un
//        /** On ne change pas le liens de validation s'il en existait déjà un pour les mails automatiques afin que si on avait envoyer au paravent un token celui-ci reste le même */
//        if (!$contact->getTokenValidation()) {
//            $this->getContactStageService()->genererTokenValidation($contact);
//        }
//        $mailData = ['stage' => $stage, 'contactStage' => $contact];
//
//        /** @var MailService $mailService */
//        $mailService = $this->getMailService();
//        try {
//            $mail = $mailService->sendMailType(CodesMailsProvider::VALIDATION_STAGE, $mailData);
//            $evenement->setDateTraitement(new DateTime());
//            $evenement->setLog("Envoie du mail#" . $mail->getId());
//            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
//        } catch (Exception $e) {
//            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
//            $evenement->setLog($e->getMessage());
//            $evenement->setDateTraitement(new DateTime());
//            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
//            return $evenement->getEtat()->getCode();
//        }
//        return $evenement->getEtat()->getCode();
    }

    //TODO : lors de la réinialisation de l'événement refaire les controle ?

    /** @var ContactStageService|null */
    protected ?ContactStageService $contactStageService = null;

    /**
     * ContactStageService
     * @throws Exception
     */
    protected function getContactStageService()
    {
        if ($this->contactStageService === null) {
            try {
                $this->contactStageService = $this->getServiceManager()->get(ContactStageService::class);
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                throw new Exception($e->getMessage());
            }
        }
        return $this->contactStageService;
    }

    /**
     * @desc retourne la liste des contacts et génére les liens de validations pour les stages s'il n'existe pas
     * @return Contact[]
     * @throws \Exception
     */
    private function preparerContacts(SessionStage $session) : array
    {
        $stages = $session->getStages();
        $contacts = [];
        /** @var Stage $stage */
        foreach ($stages as $stage){
            /** @var ContactStage $cs */
            foreach ($stage->getContactsStages() as $cs){
                $contact = $cs->getContact();
                if(!$contact->isActif()){continue;}
                if (!$contact->isActif()) {
                    continue;
                }
                if (!$contact->getEmail()) {
                    continue;
                }
                if (!filter_var($contact->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                if (!$cs->canValiderStage()) {
                    continue;
                }
                if (!$cs->sendMailAutoValidationStage()) {
                    continue;
                }
                if ($stage->hasEtatEnErreur() || $stage->hasEtatEnAlerte()) {
                    continue;
                }
                $terrain = $contact->getContactsStages();
                if(!isset($terrain)){continue;}
                if($stage->isNonEffectue()){continue;}
                if($stage->hasEtatDesactive()){continue;}
                if($stage->hasEtatEnDisponibilite()){continue;}
                $contacts[$contact->getCode()] = $contact;

                /** génération automatique du token s'il n'existe pas */
                if (!$cs->getTokenValidation()) {
                    $this->getContactStageService()->genererTokenValidation($cs);
                }
            }
        }
        return $contacts;
    }

    /** est-ce que le mails a déjà été envoyée (pour ne pas le réenvoyer au contact en cas de retraitement de l'événement */
    protected function isMailAlreadySend(Contact $contact, SessionStage $session) : bool
    {
        $mails = $contact->getMails();
        $mails =  array_filter($mails, function (Mail $mail) use ($session)  {
            if($mail->getStatusEnvoi() != Mail::SUCCESS){return false;};
//            Todo : trouver un meilleur moyen de savoir le type du mail envoyé
            $attr = $mail->getMotsClefs();
            if(!isset($attr)){return false;}
            $mailType=sprintf('MailType=%s', CodesMailsProvider::MAIL_AUTO_VALIDATIONS_STAGES);
            if(!str_contains($attr, $mailType)){return false;}
            $sessionId=sprintf('SessionStageId=%s', $session->getId());
            return (str_contains($attr, $sessionId));

        });
        return !empty($mails);
    }

}