<?php

namespace Evenement\Service\MailAuto;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Misc\Util;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Service\Contact\ContactStageService;
use Application\Service\Mail\MailService;
use DateTime;
use Evenement\Provider\EvenementEtatProvider;
use Evenement\Provider\TypeEvenementProvider;
use Evenement\Service\MailAuto\Abstract\AbstractMailAutoEvenementService;
use Exception;
use Laminas\Json\Json;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenMail\Entity\Db\Mail;

class MailAutoListeEtudiantsEncadresEvenementService extends AbstractMailAutoEvenementService
{

    //Pour les affectations, c'est géré depuis un controller
    public function findEntitiesForNewEvent(): array
    {
        return [];
    }

    //Création depuis 1 stage = on créer l'événement pour chaque contact autorisé a recevoir le mail
    public function createFromStage(Stage $stage): array
    {
        $affectation = $stage->getAffectationStage();
        if(!isset($affectation)){return [];}
        if(!$affectation->hasEtatValidee()){return [];}
        $terrain = $stage->getTerrainStage();
        if(!isset($terrain)){return [];}
        $contactStage = $stage->getContactsStages();
        $session = $stage->getSessionStage();
        $evenements = [];
        /** @var ContactStage $cs */
        foreach ($contactStage as $cs){
            $ct = $cs->getContact()->getContactForTerrain($terrain);
            if(!isset($ct)){continue;}
            if(!$ct->sendMailAutoListeEtudiantsStage()){
                continue;
            }
            $contact = $ct->getContact();
            $evenements[] = $this->create($session, $contact);
        }
        return $evenements;

    }

    /**
     * @param Stage $stage
     * @return Evenement
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function create(SessionStage $session, Contact $contact): Evenement
    {
        $now = new DateTime();
        // Vérification que l'événement n'existe pas déjà (nottament si l'on valide plusieurs affectations pour un même contact, on ne génére qu'un seul événement en réalité
        $params = [
            'type_code' => TypeEvenementProvider::MAIL_AUTO_LISTE_ETUDIANTS_STAGES,
            'etat_code' => EvenementEtatProvider::EN_ATTENTE,
            'session_id' => $session->getId(),
            'contact_id' => $contact->getId()
        ];
        $events = $this->findEvenementsWithParametres($params);
        if(!empty($events)){return current($events);}

        $eventName = sprintf("Liste des étudiants encadrés");
        $description = sprintf("Envoi aux responsable du stage la liste des étudiants qu'ils encadrent.");
        //Calcul d'une date de traitement adaptès selon la situation
        //Cette événement est généré/traité des que la validation à été effectué
        $datePlanification = $session->getDateFinCommission();
        $datePlanification->setTime(8,0);
        if($datePlanification < $now){
            $datePlanification = $now;
        }

        $parametres['session_id'] =  "".$session->getId();
        $parametres['contact_id'] = "".$contact->getId();

        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_LISTE_ETUDIANTS_STAGES);
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $parametres, $datePlanification);

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
        $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::EN_COURS));
        //Rechercher les datas nessaires pour l'envoie du mail

        $parametres = Json::decode($evenement->getParametres(), Json::TYPE_ARRAY);
        $sessionId = ($parametres['session_id']) ?? 0;
        $session = $this->getObjectManager()->getRepository(SessionStage::class)->find($sessionId);
        if (!$session) {
            $evenement->setLog("La session de stage n'as pas été trouvée");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }

        $contactId = ($parametres['contact_id']) ?? 0;
        $contact = $this->getObjectManager()->getRepository(Contact::class)->find($contactId);
        if (!$contact) {
            $evenement->setLog("Le contact n'as pas été trouvé");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }

        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        try {
            $mailData = ['contact'=>$contact, 'session' => $session];
            $mail = $mailService->sendMailType(CodesMailsProvider::MAIL_AUTO_LISTE_ETUDIANTS_STAGES, $mailData);
            $evenement->setLog(sprintf("Envoie du mail#%s à %s", $mail->getId(), $contact->getEmail()));
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
        } catch (Exception $e) {
            $evenement->setEtat($this->getEventEtat(EvenementEtatProvider::ECHEC));
            $evenement->setLog($e->getMessage());
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        return $evenement->getEtat()->getCode();
    }
}