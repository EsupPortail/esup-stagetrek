<?php

namespace Evenement\Service\MailAuto;

use Application\Entity\Db\Stage;
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

class MailAutoAffectationEvenementService extends AbstractMailAutoEvenementService
{

    //Pour les affectations, c'est géré depuis un controller
    public function findEntitiesForNewEvent(): array
    {
       return [];
    }
    /**
     * @param Stage $stage
     * @return Evenement
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function create(Stage $stage): Evenement
    {
        $session = $stage->getSessionStage();
        $etudiant = $stage->getEtudiant();
        $affectation = $stage->getAffectationStage();

        // Vérification que l'événement n'existe pas déjà
        if($stage->getDateFinCommission() < new DateTime()) {
            $params = [
                'type_code' => TypeEvenementProvider::MAIL_AUTO_AFFECTATION_VALIDEE,
                'stage_id' => $stage->getId()
            ];
        }
        else{ //Si on en as déjà un de planifié
            $params = [
                'type_code' => TypeEvenementProvider::MAIL_AUTO_AFFECTATION_VALIDEE,
                'etat_code' => EvenementEtatProvider::EN_ATTENTE,
                'stage_id' => $stage->getId()
            ];
        }
        $events = $this->findEvenementsWithParametres($params);
        if(!empty($events)){return current($events);}
        $eventName = sprintf("Affectation validée - Stage %s de %s", $stage->getLibelle(), $etudiant->getDisplayName());
        $description = sprintf("Mail signalant que l'affectation du stage de %s pour la session %s à été validée.",
            $etudiant->getDisplayName(), $stage->getSessionStage()->getLibelle()
        );
        //Calcul d'une date de traitement adaptès selon la situation
        //Cette événement est généré/traité des que la validation à été effectué
        $datePlanification = $stage->getDateFinCommission();
        $datePlanification->setTime(8,0);

        $parametres['session-id'] = $session->getId();
        $parametres['stage-id'] = $stage->getId();
        $parametres['etudiant-id'] = $stage->getEtudiant()->getId();
        $parametres['affectation-stage-id'] = $affectation->getId();
        $parametres['stage'] = $stage->getLibelle();
        $parametres['etudiant'] = $etudiant->getDisplayName();

        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_AFFECTATION_VALIDEE);
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $parametres, $datePlanification);

        $this->ajouter($evenement);
        return $evenement;
    }

    public function traiter(Evenement $evenement): string
    {
        //On nettoie les logs
        $evenement->setLog("");
        $evenement->setDateTraitement(new DateTime());

        /** @var MailService $mailService */
        $mailService = $this->getMailService();
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
        $etudiant = $stage->getEtudiant();
        $affectation = $stage->getAffectationStage();
        if (!$affectation || !$affectation->hasEtatValidee()) {
            $evenement->setLog("L'affectation du stage n'est pas validée par la commission.");
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return $evenement->getEtat()->getCode();
        }
        try {
            $mailData = ['stage'=>$stage];
            $mail = $mailService->sendMailType(CodesMailsProvider::AFFECTATION_STAGE_VALIDEE, $mailData);
            $evenement->setLog(sprintf("Envoie du mail#%s à l'étudiant.e" . $mail->getId(), $etudiant->getDisplayName()));
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

    /** ContactStageService
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