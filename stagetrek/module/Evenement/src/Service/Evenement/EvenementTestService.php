<?php

namespace Evenement\Service\Evenement;

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
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnicaenEvenement\Entity\Db\Etat;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenEvenement\Service\Etat\EtatServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceInterface;
use UnicaenEvenement\Service\Type\TypeServiceAwareTrait;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;

class EvenementTestService implements EvenementServiceInterface
{

    use EtatServiceAwareTrait;
    use EvenementServiceAwareTrait;
    use TypeServiceAwareTrait;
    use MailServiceAwareTrait;

    /**
     * @return Evenement
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function create(?string $mailTo = null): Evenement
    {
        $now = new DateTime();
        $etat = $this->getEtatEvenementService()->findByCode(EvenementEtatProvider::EN_ATTENTE);
        $type = $this->getTypeService()->findByCode(TypeEvenementProvider::TEST);
        $eventName = $type->getLibelle();
        $description =$type->getDescription();
        $param['mail-to'] = $mailTo;
        $evenement = $this->evenementService->createEvent($eventName, $description, $etat, $type, $param, $now);
        $this->ajouter($evenement);
        return $evenement;
    }

    public function traiter(Evenement $evenement): string
    {
        try {
            $failure = $this->getEtatEvenementService()->findByCode(EvenementEtatProvider::ECHEC);
            $success = $this->getEtatEvenementService()->findByCode(EvenementEtatProvider::SUCCES);

            $now = new DateTime();
            $evenement->setDateTraitement($now);
            $parametres = $evenement->getParametres();
            if(isset($parametres) && $parametres != ""){
                $parametres = Json::decode($evenement->getParametres(), Json::TYPE_ARRAY);
                $mailTo = ($parametres['mail-to']) ?? null;
            }
            else{
                $mailTo = null;
            }

            $log = sprintf("Traitement de l'événement de test le %s à %s", $now->format('Y-m-d'), $now->format('H:i s\s'));

            if(isset($mailTo) && $mailTo != "") {
                $subject = "Test de traitement des événements";
                $body = "<p>Bonjour,</p>";
                $body .= "<p>Ceci est un mail de test. Merci de ne pas en tenir compte</p>";
                $mail = $this->getMailService()->sendMail($mailTo, $subject, $body);
                $log .= sprintf("<br/> Un mail de test a été envoyé à %s", $mailTo);
                $log .= sprintf("<br/> Code de statut du mail : %s", $mail->getStatusEnvoi());
            }
            $evenement->setLog($log);
            $this->changerEtat($evenement, $success);
        } catch (Exception $e) {
            $evenement->setEtat($failure);
            $evenement->setLog("Echec lors de l'événement de test : <br />".$e->getMessage());
            $this->changerEtat($evenement, $failure);
            return $evenement->getEtat()->getCode();
        }
        return $evenement->getEtat()->getCode();
    }


    /**
     * @param Evenement $evenement
     * @return Evenement
     */
    public function ajouter(Evenement $evenement): Evenement
    {
        $this->evenementService->create($evenement);
        return $evenement;
    }

    /**
     * @param Evenement $evenement
     * @param Etat $etat
     * @return Evenement
     */
    public function changerEtat(Evenement $evenement, Etat $etat): Evenement
    {
        $evenement->setEtat($etat);
        $this->evenementService->update($evenement);

        return $evenement;
    }

    /**
     * @param Evenement $evenement
     * @return Evenement
     */
    public function supprimer(Evenement $evenement): Evenement
    {
        $this->evenementService->delete($evenement);
        return $evenement;
    }

}