<?php

namespace Evenement\Service\MailAuto;

use Application\Entity\Db\Stage;
use Application\Misc\Util;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Provider\Roles\UserProvider;
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
use UnicaenUtilisateur\Entity\Db\User;

class MailAutoStageValidationEffectueEvenementService extends AbstractMailAutoEvenementService
{
    /**
     * @return array
     */

    //Gestion depuis le controlleur
    public function findEntitiesForNewEvent(): array
    {
        return [];
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(Stage $stage) : Evenement
    {
        $session = $stage->getSessionStage();
        $etudiant = $stage->getEtudiant();
        $eventName = sprintf("Validation effectuée - Stage %s de %s", $stage->getLibelle(), $etudiant->getDisplayName());
        $description = sprintf("Mail signalant que la validation du stage de %s pour la session %s à été effectuée.", $etudiant->getDisplayName(), $stage->getSessionStage()->getLibelle());
        //Calcul d'une date de traitement adaptès selon la situation
        //Cette événement est généré/traité des que la validation à été effectué
        $datePlanification = new DateTime();
        $parametres['session_id'] = "".$session->getId();
        $parametres['stage_id'] = "".$stage->getId();
        $parametres['etudiant_id'] = "".$stage->getEtudiant()->getId();
        $parametres['stage'] = $stage->getLibelle();
        $parametres['etudiant'] = $etudiant->getDisplayName();
        $params = ['type_code' => TypeEvenementProvider::MAIL_AUTO_STAGE_VALIDATION_EFFECTUE, 'stage_id' => $stage->getId()];
        // Vérification que l'événement n'existe pas déjà
        $events = $this->findEvenementsWithParametres($params);
        $parametres['num'] = sizeof($events) + 1;
        $etat = $this->getEventEtat(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::MAIL_AUTO_STAGE_VALIDATION_EFFECTUE);
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $parametres, $datePlanification);
        $this->ajouter($evenement);
        return $evenement;
    }

    public function traiter(Evenement $evenement): string
    {
        //On nettoie les logs
        $today = new DateTime();
        $log = sprintf("Début du traitement de l'événement le %s à %s <br/>", $today->format('d/m/y'), $today->format('H:i:s'));
        $evenement->setLog($log);
        $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::EN_COURS));
        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        //Rechercher les datas nessaires pour l'envoie du mail
        $parametres = Json::decode($evenement->getParametres(), Json::TYPE_ARRAY);
        $stageId = ($parametres['stage_id']) ?? 0;
        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($stageId);
        if (!$stage) {
            $evenement->setDateTraitement(new DateTime());
            $log .= "Le stage correspondant au mail n'as pas été trouvée";
            $evenement->setLog($log);
            $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
            return EvenementEtatProvider::ECHEC;
        }
        $num = ($parametres['num']) ?? 1;
        $etudiant = $stage->getEtudiant();
        $validation = $stage->getValidationStage();
        if ($num == 1) {
            try {
                $mailData = ['stage' => $stage];
                $mail = $mailService->sendMailType(CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE, $mailData);
                $log .= sprintf("Envoie du mail#%s à l'étudiant".Util::POINT_MEDIANT."e %s <br/>", $mail->getId(), $etudiant->getDisplayName());
            } catch (Exception $e) {
                $evenement->setDateTraitement(new DateTime());
                $log .= sprintf("Une erreur est survenue : %s <br/>", $e->getMessage());
                $evenement->setLog($log);
                $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
                return EvenementEtatProvider::ECHEC;
            }
        }
        //Envoie du mail à la commission si besoin
        if ($validation->getCommentaireCache() != "") {
            try {
                $appUSer = $this->getObjectManager()->getRepository(User::class)->findOneBy(['username' => UserProvider::APP_USER]);
                if (isset($appUSer)) {
                    $to = $appUSer->getEmail();
                    $sujet = sprintf("Validation du stage %s de %s", $stage->getLibelle(), $etudiant->getDisplayName());
                    if ($num > 1) {
                        $sujet .= " [Modification]";
                    }
                    $corps = sprintf("<p>Le stage %s de %s a été %s par %s</p>", $stage->getLibelle(), $etudiant->getDisplayName(), ($stage->getValidationStage()->isValide()) ? "validé" : "invalidé", $validation->getValidateBy());
                    if ($validation->getCommentaireCache() != "") {
                        $corps .= sprintf("<hr/><p><b>Commentaires portées à l'attention de la commission :</b><div>%s</div></p>", $validation->getCommentaireCache());
                    }
                    $mail = $this->getMailService()->sendMail($to, $sujet, $corps);
                    if(isset($mail)) {
                        $motsClef = ['stageId=' . $stage->getId()];
                        $mail->setMotsClefs($motsClef);
                        $this->getMailService()->update($mail);
                    }
                }
            } catch (Exception $e) {
                $evenement->setDateTraitement(new DateTime());
                $log .= sprintf("Une erreur est survenue : %s <br/>", $e->getMessage());
                $evenement->setLog($log);
                $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::ECHEC));
                return EvenementEtatProvider::ECHEC;
            }
        }
        $dateFin = new DateTime();
        $evenement->setDateTraitement($dateFin);
        $log = sprintf("Fin du traitement de l'événement le %s à %s <br/>", $dateFin->format('d/m/y'), $dateFin->format('H:i:s'));
        $evenement->setLog($log);
        $this->changerEtat($evenement, $this->getEventEtat(EvenementEtatProvider::SUCCES));
        return EvenementEtatProvider::SUCCES;
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