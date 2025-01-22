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

class MailAutoStageRappelValidationEvenementService extends AbstractMailAutoEvenementService
{
    //Fonction qui donne les tuples
    /**
     * @return ContactStage[]
     * @throws \Exception
     */
    public function findEntitiesForNewEvent(): array
    {
        /** @var int $parametrePlanification */
        $parametreNbJourRappel = $this->getParametreService()->getParametreValue(Parametre::DELAI_RAPPELS);
        $parametreProlonguationToken = $this->getParametreService()->getParametreValue(Parametre::DUREE_TOKEN_VALDATION_STAGE);
        //Sessions dont les dates sont proches du début de la phase de validation
        $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
        $sessions = array_filter($sessions, function (SessionStage $session) use($parametreNbJourRappel, $parametreProlonguationToken){
            if($session->hasEtatAlerte() || $session->hasEtatError()){return false;}
            $date1 = clone($session->getDateFinValidation());
            $date1->sub(new DateInterval('P' . $parametreNbJourRappel . 'D'));
            $date2 = clone($session->getDateFinValidation());
            $date1->sub(new DateInterval('P' . $parametreNbJourRappel . 'D'));
            $date1->setTime(0,0);
            $date2->setTime(23,59);
//            Choix fait d'autoriser des mails de rappels 1 mois après la date de fin du stage afin de pouvoir gerer des échecs éventuelle
            $date2->add(new DateInterval('P'.$parametreProlonguationToken.'D'));
            $today = new DateTime();
            return ($date1 <= $today && $today <=$date2);
        });

        /** @var Stage[] $stages */
        $stages = [];
        /** @var SessionStage $session */
        foreach ($sessions as $session){
            /** @var Stage $stage */
            foreach ($session->getStages() as $stage){
                if($stage->hasEtatEnErreur() || $stage->hasEtatEnAlerte()){
                    continue;
                }
                //Pas de mail de rappel si la validation du stage a été effectué
                if($stage->getValidationStage() && $stage->getValidationStage()->validationEffectue()){
                    continue;
                }
                if(!$stage->getAffectationStage() || !$stage->getAffectationStage()->hasEtatValidee()){
                    continue;
                }
//                Choix fait d'autorisé les mails de rappels après la date fin
//                if ($stage->getDateFinValidation() < $today) { continue;}
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
                    'type_code' => TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION,
                    'session_id' => $session->getId(),
                    'stage_id' => $stage->getId(),
                    'contact_stage_id' => $contact->getId(),
                ];
                // Vérification que l'événement n'existe pas déjà
                $events = $this->findEvenementsWithParametres($params);
                if(!empty($events)){continue;}
                $contacts[] = $contact;
            }
        }
        return $contacts;
    }

    /**
     * @param ContactStage $contactStage
     * @return Evenement
     * @throws \Exception
     */
    public function create(ContactStage $contactStage) : Evenement
    {
        $stage = $contactStage->getStage();
        $session = $stage->getSessionStage();
        $etudiant = $stage->getEtudiant();
        $destinataireName = trim($contactStage->getDisplayName());
        if (!$destinataireName) {
            $destinataireName = strtolower(trim($contactStage->getEmail()));
        }

        $eventName = sprintf("Rappel de validation - Stage %s de %s à destination de %s", $stage->getLibelle(), $etudiant->getDisplayName(), $destinataireName);
        $description = sprintf("Mail automatique de rappel pour la validation du stage de %s pour la session %s envoyé à %s",
            $etudiant->getDisplayName(), $stage->getSessionStage()->getLibelle(), $destinataireName
        );

        //Calcul d'une date de traitement adaptès selon la situation
        $datePlanification = clone($stage->getDateFinValidation());
        $parametreNbJourRappel = $this->getParametreService()->getParametreValue(Parametre::DELAI_RAPPELS);
        $datePlanification->sub(new DateInterval('P' . $parametreNbJourRappel . 'D'));
        $datePlanification->setTime(8, 0);
        $today = new DateTime();
        $parametres['session-id'] = "".$session->getId();
        $parametres['stage-id'] = "".$stage->getId();
        $parametres['etudiant-id'] = "".$stage->getEtudiant()->getId();
        $parametres['contact-stage-id'] = "".$contactStage->getId();
        $parametres['stage'] = $stage->getLibelle();
        $parametres['etudiant'] = $etudiant->getDisplayName();
        $parametres['contact'] = $destinataireName;


        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION);

        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $parametres, $datePlanification);

        //Mise en erreur automatique d'événement selon certaines situations
        $dateFinValidatioProlongee = clone($stage->getDateFinValidation());
        $parametreProlonguationToken = $this->getParametreService()->getParametreValue(Parametre::DUREE_TOKEN_VALDATION_STAGE);
        $dateFinValidatioProlongee->add(new DateInterval('P'.$parametreProlonguationToken.'D'));
        if ($dateFinValidatioProlongee < $today) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail rappel plus de ".$parametreProlonguationToken." jours après la date de fin de la phase de validation");
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
        }
        elseif (!$contactStage->canValiderStage()) {
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
        if($stage->getValidationStage() && $stage->getValidationStage()->validationEffectue()){
            $evenement->setLog("Le stage a été validé avant l'envoie du mail de rappel");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
            return $evenement->getEtat()->getCode();
        }
        if (!$contact) {
            $evenement->setLog("Le contact correspondant au mail n'as pas été trouvée");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        //Quelques vérification avant pour éviter des erreurs
        $today = new DateTime();
        $dateFinValidatioProlongee = clone($stage->getDateFinValidation());
        $parametreProlonguationToken = $this->getParametreService()->getParametreValue(Parametre::DUREE_TOKEN_VALDATION_STAGE);
        $dateFinValidatioProlongee->add(new DateInterval('P'.$parametreProlonguationToken.'D'));
        if ($dateFinValidatioProlongee < $today) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail rappel plus de ".$parametreProlonguationToken." jours après la date de fin de la phase de validation");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        if ($stage->hasEtatEnErreur()) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail de rappel d'un stage en erreur");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        if ($stage->hasEtatEnAlerte()) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail de rappel d'un stage en alerte");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }

        //Génération d'un token de validation si le stage n'en as pas déjà un
        /** On ne change pas le liens de validation s'il en existait déjà un pour les mails automatiques afin que si on avait envoyer au paravent un token celui-ci reste le même */
        if (!$contact->getTokenValidation() || $contact->getTokenExpirationDate() < $today) {
            $this->getContactStageService()->genererTokenValidation($contact);
        }
        $mailData = ['stage' => $stage, 'contactStage' => $contact];

        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        try {
            $mail = $mailService->sendMailType(CodesMailsProvider::VAlIDATION_STAGE_RAPPEL, $mailData);
            $evenement->setDateTraitement(new DateTime());
            $evenement->setLog("Envoie du mail#" . $mail->getId());
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
        } catch (Exception $e) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog($e->getMessage());
            return $evenement->getEtat()->getCode();
        }
        return $evenement->getEtat()->getCode();
    }

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