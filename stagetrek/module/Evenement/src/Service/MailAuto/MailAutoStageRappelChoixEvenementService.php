<?php

namespace Evenement\Service\MailAuto;

use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Provider\Parametre\ParametreProvider;
use Application\Service\Mail\MailService;
use DateInterval;
use DateTime;
use Evenement\Provider\EvenementEtatProvider;
use Evenement\Provider\TypeEvenementProvider;
use Evenement\Service\MailAuto\Abstract\AbstractMailAutoEvenementService;
use Exception;
use Laminas\Json\Json;
use UnicaenEvenement\Entity\Db\Evenement;

class MailAutoStageRappelChoixEvenementService extends AbstractMailAutoEvenementService
{
    /**
     * @return Stage[]
     * @throws \Exception
     */
    public function findEntitiesForNewEvent(): array
    {
        $parametreNbJourRappel = $this->getParametreService()->getParametreValue(ParametreProvider::DELAI_RAPPELS);

        //Sessions dont les dates sont proches du début de la phase de validation
        $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
        $sessions = array_filter($sessions, function (SessionStage $session) use($parametreNbJourRappel){
            if($session->hasEtatAlerte() || $session->hasEtatError()){return false;}
            $date1 = clone($session->getDateFinChoix());
            $date1->sub(new DateInterval('P' . $parametreNbJourRappel . 'D'));
            $date2 = clone($session->getDateFinChoix());
            $date1->sub(new DateInterval('P' . $parametreNbJourRappel . 'D'));
            $date1->setTime(0,0);
            $date2->setTime(23,59);
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
                if($stage->getPreferences()->count() >0){continue;}

                $params = [
                    'type_code' => TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX,
                    'session_id' => $session->getId(),
                    'stage_id' => $stage->getId(),
                    'etudiant_id' => $stage->getEtudiant()->getId(),
                ];
                // Vérification que l'événement n'existe pas déjà
                $events = $this->findEvenementsWithParametres($params);
                if(!empty($events)){continue;}
                $stages[]=$stage;
            }
        }
        return $stages;
    }

    /**
     * @param Stage $stage
     * @return Evenement
     * @throws Exception
     */
    public function create(Stage $stage) : Evenement
    {
        $session = $stage->getSessionStage();
        $etudiant = $stage->getEtudiant();
        $eventName = sprintf("Rappel de la phase de définition des préférences - Stage %s de %s", $stage->getLibelle(), $etudiant->getDisplayName());
        $description = sprintf("Mail automatique de rappel pour la définition des préférences du stage de %s pour la session %s",
            $etudiant->getDisplayName(), $stage->getSessionStage()->getLibelle()
        );

        //Calcul d'une date de traitement adaptès selon la situation
        $datePlanification = clone($stage->getDateFinChoix());
        $parametreNbJourRappel = $this->getParametreService()->getParametreValue(ParametreProvider::DELAI_RAPPELS);
        $datePlanification->sub(new DateInterval('P' . $parametreNbJourRappel . 'D'));
        $datePlanification->setTime(8, 0);

        $today = new DateTime();
        $parametres['session-id'] = "".$session->getId();
        $parametres['stage-id'] = "".$stage->getId();
        $parametres['etudiant-id'] = "".$stage->getEtudiant()->getId();
        $parametres['stage'] = $stage->getLibelle();
        $parametres['etudiant'] = $etudiant->getDisplayName();

        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX);
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $parametres, $datePlanification);

        //Mise en erreur automatique d'événement selon certaines situations
        if ($stage->getDateFinChoix() < $today) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog("Impossible d'envoyer automatiquement un rappel de définition des préférences après la date de fin de la phase de choix.");
        }

        $this->ajouter($evenement);
        return $evenement;
    }

    public function traiter(Evenement $evenement): string
    {
//        if($evenement->getEtat()->getCode() != EvenementEtatProvider::EN_ATTENTE){
//            return $evenement->getEtat()->getCode();
//        }
        $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::EN_COURS));
        //Rechercher les datas nessaires pour l'envoie du mail
        $parametres = Json::decode($evenement->getParametres(), Json::TYPE_ARRAY);
        $stageId = ($parametres['stage-id']) ?? 0;
        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($stageId);
        if (!$stage) {
            $evenement->setLog("Le stage correspondant au mail n'as pas été trouvée");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        //Si l'étudiant as définit des préférences entre la planification et l'événement
        if($stage->getPreferences()->count() >0){
            $evenement->setLog("Des préférences ont été définit entre la date de planification et l'envoie du mail de rappel");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
            return $evenement->getEtat()->getCode();
        }

        //Quelques vérification avant pour éviter des erreurs
        $today = new DateTime();
        if ($stage->getDateFinChoix() < $today) {
            $evenement->setLog("Impossible d'envoyer automatiquement le mail de rappel de définition des préférences après la date de fin de la phase de choix");
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
        $mailData = ['stage' => $stage];

        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        try {
            $mail = $mailService->sendMailType(CodesMailsProvider::STAGE_DEBUT_CHOIX_RAPPEL, $mailData);
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
}