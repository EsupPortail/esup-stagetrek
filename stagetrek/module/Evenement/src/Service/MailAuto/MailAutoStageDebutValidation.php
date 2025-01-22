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

class MailAutoStageDebutValidation extends AbstractMailAutoEvenementService
{
    //Fonction qui donne les tuples
    /**
     * @return ContactStage[]
     * @throws Exception
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
            return ($date1 <= $today && $today <=$date2);
        });

        /** @var Stage[] $stages */
        $stages = [];
        $today = new DateTime();
        /** @var SessionStage $session */
        foreach ($sessions as $session){
            /** @var Stage $stage */
            foreach ($session->getStages() as $stage){
                if($stage->isNonEffectue()){continue;}
                if($stage->getValidationStage()->isValide()){continue;}
                if($stage->hasEtatEnErreur() || $stage->hasEtatEnAlerte()){
                    continue;
                }
                if(!$stage->getAffectationStage() || !$stage->getAffectationStage()->hasEtatValidee()){
                    continue;
                }
                if ($stage->getDateFinValidation() < $today) { continue;}
                $stages[]=$stage;
            }
        }
        /** @var ContactStage[] $contacts */
        $contacts=[];
        foreach ($stages as $stage) {
            /** @var ContactStage $contact */
            foreach ($stage->getContactsStages() as $contact) {
                if (!$contact->canValiderStage()) {
                    continue;
                }
                if (!$contact->isActif()) {
                    continue;
                }
                if (!$contact->getEmail()) {
                    continue;
                }
                if (!filter_var($contact->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $stage=$contact->getStage();
                $session=$stage->getSessionStage();
                $params = [
                    'type_code' => TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
                    'session_id' => $session->getId(),
                    'stage_id' => $stage->getId(),
                    'contact_stage_id' => $contact->getId(),
                ];
                // Vérification que l'événement n'existe pas déjà
                $events = $this->findEvenementsWithParametres($params);
                if(!empty($events)){
                    continue;}
                $contacts[] = $contact;
            }
        }
        return $contacts;
    }

    /**
     * @param ContactStage $contactStage
     * @return Evenement
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function create(ContactStage $contactStage): Evenement
    {
        $stage = $contactStage->getStage();
        $session = $stage->getSessionStage();
        $etudiant = $stage->getEtudiant();
        $destinataireName = trim($contactStage->getDisplayName());
        if (!$destinataireName) {
            $destinataireName = strtolower(trim($contactStage->getEmail()));
        }

        $eventName = sprintf("Début de la phase de validation - Stage %s de %s à destination de %s", $stage->getLibelle(), $etudiant->getDisplayName(), $destinataireName);
        $description = sprintf("Mail automatique de demande de validation du stage de %s pour la session %s envoyé à %s",
            $etudiant->getDisplayName(), $stage->getSessionStage()->getLibelle(), $destinataireName
        );

        $today =  new DateTime();
        //Calcul d'une date de traitement adaptès selon la situation
        $datePlanification = $stage->getDateDebutValidation();
        $datePlanification->setTime(8, 0);
        if($datePlanification < $today){
            $datePlanification = $today;
        }

        $parametres['session-id'] = $session->getId();
        $parametres['stage-id'] = $stage->getId();
        $parametres['etudiant-id'] = $stage->getEtudiant()->getId();
        $parametres['contact-stage-id'] = $contactStage->getId();
        $parametres['stage'] = $stage->getLibelle();
        $parametres['etudiant'] = $etudiant->getDisplayName();
        $parametres['contact'] = $destinataireName;

        /** On regarde s'il n'y en a pas déjà un en attente */
        $params = [
            'type_code' => TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE,
            'session_id' => "".$session->getId(),
            'etat_code' => EvenementEtatProvider::EN_ATTENTE,
            'stage_id' => "".$stage->getId(),
            'contact_stage_id' => "".$contactStage->getId(),
        ];
        // Vérification que l'événement n'existe pas déjà
        $events = $this->findEvenementsWithParametres($params);
        if(!empty($events)){
            return current($events);
        }


        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE);
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $parametres, $datePlanification);

        //Mise en erreur automatique d'événement selon certaines situations
//        Faut car on planifie le mail avant la date de début, ce cas fait que les événements sont automatiquement mis en erreur et donc échoue
//        if ($today < $stage->getDateDebutValidation()) {
//            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
//            $evenement->setLog("Impossible d'envoyer automatiquement une demande de validation avant la date de début de la phase de validation.");
//        }
        if (!$contactStage->canValiderStage()) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog("Le contact n'est pas autorisé à valider les stages");
        }
        elseif (!$contactStage->getEmail()) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog("L'adresse mail du contact n'est pas définie");
        }
        elseif (!filter_var($contactStage->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog("L'adresse mail du contact n'est pas une adresse mail valide");
        }

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
        $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::EN_COURS));
        //Rechercher les datas nessaires pour l'envoie du mail
        $parametres = Json::decode($evenement->getParametres(), Json::TYPE_ARRAY);
        $stageId = ($parametres['stage-id']) ?? 0;
        $contactId = ($parametres['contact-stage-id']) ?? 0;
        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($stageId);
        /** @var ContactStage $contact */
        $contact = $this->getObjectManager()->getRepository(ContactStage::class)->find($contactId);
        if (!$stage) {
            $evenement->setLog("Le stage correspondant au mail n'as pas été trouvée");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        if (!$contact) {
            $evenement->setLog("Le contact correspondant au mail n'as pas été trouvée");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        //Quelques vérification avant pour éviter des erreurs
        $today = new DateTime();
        if ($stage->getValidationStage()->validationEffectue() && $stage->getDateFinValidation() < $today) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail pour un stage déjà validé après la date de fin de la phase de validation");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        if ($stage->hasEtatEnErreur()) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail de validation d'un stage en erreur");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        if ($stage->hasEtatEnAlerte()) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail de validation d'un stage en alerte");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }

        //Génération d'un token de validation si le stage n'en as pas déjà un
        /** On ne change pas le liens de validation s'il en existait déjà un pour les mails automatiques afin que si on avait envoyer au paravent un token celui-ci reste le même */
        if (!$contact->getTokenValidation()) {
            $this->getContactStageService()->genererTokenValidation($contact);
        }
        $mailData = ['stage' => $stage, 'contactStage' => $contact];

        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        try {
            $mail = $mailService->sendMailType(CodesMailsProvider::VALIDATION_STAGE, $mailData);
            $evenement->setDateTraitement(new DateTime());
            $evenement->setLog("Envoie du mail#" . $mail->getId());
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
        } catch (Exception $e) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog($e->getMessage());
            $evenement->setDateTraitement(new DateTime());
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        return $evenement->getEtat()->getCode();
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

}