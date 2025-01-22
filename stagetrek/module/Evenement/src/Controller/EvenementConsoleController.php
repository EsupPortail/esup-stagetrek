<?php

namespace Evenement\Controller;

use DateTime;
use Exception;
use Laminas\Router\RouteInterface;
use Laminas\Uri\Http;
use RuntimeException;
use Unicaen\Console\ColorInterface;
use Unicaen\Console\Request as ConsoleRequest;
use UnicaenApp\Exception\LogicException;
use UnicaenEvenement\Entity\Db\Etat;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenEvenement\Entity\Db\Journal;

/** Surcharge de UnicaenEvenement\Controller\EvenementConsoleController issue de la librairie permettant d'inclure nottament les options --uri-host requis pour certains mails automatique  */
class EvenementConsoleController extends \UnicaenEvenement\Controller\EvenementConsoleController
{

    const ACTION_TRAITER_EVENEMENTS = "traiter-evenements";

    public function traiterEvenementsAction(): void
    {//Cg Concole/TraiterEvenementCommande
        Throw new \Exception("Usage : changement de méthode, passer par Symphonie console a la place.");
        exit(0);
//        try {
//            $this->useConsoleUri();
//        } catch (RuntimeException $e) {
//            $this->getConsole()->writeLine("-------------------", ColorInterface::RED);
//            $this->getConsole()->writeLine($e->getMessage(), ColorInterface::RED);
//            $this->getConsole()->writeLine("-------------------", ColorInterface::RED);
//            exit(0);
//        }
//        //On remplace le code UnicaenEvenement pour le momment afin d'intégrer l'id des évements au besoins
//        $request = $this->getRequest();
//        $texte = "";
//        if (!$request instanceof ConsoleRequest) {
//            throw new \RuntimeException('Cette action doit être appelée depuis une console !');
//        }
//        $this->cleartOldEventEntry();
//        $this->assertNoActionIsRunning();
//        $evenements = $this->getEvenementService()->getEvenementsByEtat(Etat::EN_ATTENTE);
//        if (empty($evenements)) { //Pour ne pas sauvegarder des logs inutiles, on vérifie avant
//            $this->getConsole()->writeLine("\nAucun événements à trairer", ColorInterface::BLUE);
//            $this->getConsole()->writeLine("\n-------------------", ColorInterface::GREEN);
//            $this->getConsole()->writeLine("Fin du traitement", ColorInterface::GREEN);
//            exit(0);
//        }
////        On trie les événements par date de planification pour faire en priorité les plus ancien
//        usort($evenements, function (Evenement $e1, Evenement $e2) {
//            return $e1->getDatePlanification() < $e2->getDatePlanification();
//        });
//
//
//        $journal = new Journal();
//        $journal->setDate(new DateTime());
//        $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::EN_COURS));
//        $this->getJournalService()->create($journal);
//        $start = new DateTime();
//        $this->getConsole()->writeLine("\n* " . ($titre = "Traitement des événements en attente") . " *\n", ColorInterface::LIGHT_GREEN);
//        $this->getConsole()->writeLine("Debut du traitement", ColorInterface::GREEN);
//        $this->getConsole()->writeLine("-------------------\n", ColorInterface::GREEN);
//        $texte .= "Traitement des événements en attente <br/>";
//        $texte .= "Début du traitement : " . (new DateTime())->format('d/m/Y H:i:s') . "<br/>";
//        $journal->setLog($texte);
//        $this->getJournalService()->update($journal);
//        $nbOp = 0;
//        $nbError = 0;
//        foreach ($evenements as $evenement) {
//            // calcul du temps d'exécution
//            $executionTime = (new DateTime())->getTimestamp() - $start->getTimestamp();
//            if ($executionTime >= $this->maxExecutionTime) {
//                // si le temps d'exécution maximal est dépassé on quitte la procédure
//                break;
//            }
//            try {
//                if ($evenement !== null) {
//                    $this->getConsole()->writeLine(sprintf("- Début du traitement de l'événement #%s", $evenement->getId()));
//                    $texte .= "Début du traitement  de l'événement #" . $evenement->getId() . " <br/>";
//                    $evenement = $this->evenementGestionService->traiterEvent($evenement);
//                    if ($evenement->getEtat()->getCode() == Etat::SUCCES) {
//                        $this->getConsole()->writeLine(sprintf("- Evenement #%s - %s", $evenement->getId(), $evenement->getEtat()->getCode()), ColorInterface::GREEN);
//                        $texte .= sprintf("Evenement #%s - %s <br/>", $evenement->getId(), $evenement->getEtat()->getCode());
//                    } else {
//                        $this->getConsole()->writeLine(sprintf("- Echec de traitement de l'événement #%s", $evenement->getId()), ColorInterface::RED);
//                        $texte .= sprintf("Evenement #%s - %s <br/>", $evenement->getId(), $evenement->getEtat()->getCode());
//                        $nbError++;
//                    }
//                    $nbOp++;
//                }
//            } catch (Exception $e) {
//                $this->getConsole()->writeLine(sprintf("- échec du traitement de l'événement #%s", $evenement->getId()), ColorInterface::RED);
//                $texte .= "<strong> Échec du traitement </strong> <br/>";
//            }
//        }
//        $this->getConsole()->writeLine("\n-------------------", ColorInterface::GREEN);
//        $this->getConsole()->writeLine("Fin du traitement", ColorInterface::GREEN);
//        $texte .= "Fin du traitement : " . (new DateTime())->format('d/m/Y H:i:s') . "";
//        $this->getConsole()->writeLine("\nRapport", ColorInterface::CYAN);
//        $this->getConsole()->writeLine("-------", ColorInterface::CYAN);
//        $this->getConsole()->writeLine(sprintf("* Nombre d'opérations : %s", $nbOp), ColorInterface::CYAN);
//        if($nbError>0){
//           $this->getConsole()->writeLine(sprintf("* Nombre d'opérations échouées : %s", $nbOp), ColorInterface::RED);
//        }
//        $this->getConsole()->writeLine(sprintf("* Temps estimé : %s", $diff = $start->diff(new DateTime())->format('%Hh %Im %Ss')), ColorInterface::CYAN);
//        $journal->setLog($texte);
//        if($nbError==0){
//            $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::SUCCES));
//        }
//        else{
//            $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::ECHEC));
//        }
//        $this->getJournalService()->update($journal);
    }

    protected function isConsoleAction(): bool
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Cette action doit être appelée depuis une console !');
        }
        return true;
    }

//    En jours
    const CONSERVATION_LOG_TIME = 30;

    private function cleartOldEventEntry()
    {
        $dateSuppression = new DateTime();
        $dateSuppression->sub(new \DateInterval('P' . self::CONSERVATION_LOG_TIME . 'D'));
        $dateSuppression->setTime(0, 0);
        $evenements = $this->getEvenementService()->getEvenements();
        /** @var Evenement $evenement */
        foreach ($evenements as $evenement) {
            $dateTraitement = $evenement->getDateTraitement();
//            Suppression d'un événement trop vieux
            if (isset($dateTraitement) && $dateTraitement < $dateSuppression) {
                $this->getEvenementService()->delete($evenement);
            }
            //Mise en echec d'un événement en cours de traitement trop vieux
            if ($evenement->getEtat()->getCode() == Etat::EN_COURS) {
                // calcul du temps d'exécution
                $time = new DateTime();
                $executionTime = $time->getTimestamp() - $evenement->getDatePlanification()->getTimestamp();
                $maxExecutionTime = 60 * 10;//on fixe arbitrairement a 10 min soit 2 foix le temps entre 2 lancement automatique de traiter evenement pour être sur
                if ($executionTime >= $maxExecutionTime) {
                    $log = $evenement->getLog();
                    $log .= sprintf("<br/>-----------<br/>");
                    $log .= sprintf("Mise en echec le %s à %s car temps d'exécution trop long <br/>", $time->format('d/m/Y'), $time->format('H:i'));
                    $log .= sprintf("-----------<br/>");
                    $this->getConsole()->writeLine("\n* " . ($titre = "Mise en échec de l'événement #" . $evenement->getId()) . " *\n", ColorInterface::RED);
                    $evenement->setLog($log);
                    $evenement->setEtat($this->getEtatEvenementService()->findByCode(Etat::ECHEC));
                    $evenement->setDateFin($time);
                    $this->getEvenementService()->update($evenement);
                }
            }
        }
        $logs = $this->getJournalService()->getJounaux();
        /** @var Journal $log */
        foreach ($logs as $log) {
            if ($log->getDate() < $dateSuppression) {
                $this->getJournalService()->delete($log);
            }
        }
    }

    private function assertNoActionIsRunning()
    {
        //Test pour que si un journal est en cours depuis trop longtemp, on le met en echec dans le journal pour cause de timeout
        $journal = $this->getJournalService()->getDernierJournal();
        if (isset($journal) && $journal->getEtat()->getCode() == Etat::EN_COURS) {
            $time = new DateTime();
            $executionTime = $time->getTimestamp() - $journal->getDate()->getTimestamp();
            if ($executionTime >= $this->maxExecutionTime) {
//            if(true) { // pour les test
                $log = $journal->getLog();
                $log .= sprintf("-----------<br/>");
                $log .= sprintf("Mise en echec le %s à %s car temps d'exécution trop long <br/>", $time->format('d/m/Y'), $time->format('H:i'));
                $log .= sprintf("-----------<br/>");
                $this->getConsole()->writeLine("\n* " . ($titre = "Mise en échec du journal #" . $journal->getId()) . " *\n", ColorInterface::RED);
                $journal->setLog($log);
                $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::ECHEC));
                $this->getJournalService()->update($journal);
            }
        }
        if ($this->getJournalService()->isRunning()) {
            throw new \RuntimeException('Un traitement est déjà en cours !');
        }
    }


//    Permet de désactiver depuis la configuration une action
    protected function actionAllowed(string $action): bool
    {
        if (!isset($this->consoleConfig)) {
            return true;
        }
        if (!isset($this->consoleConfig['actions'])) {
            return true;
        }
        if (!isset($this->consoleConfig['actions'][$action])) {
            return true;
        }
        if (!$this->consoleConfig['actions'][$action]) {
            $msg = sprintf("L'action %s a été désactivé dans la configuration", $action);
            throw new \RuntimeException($msg);
        }
        return true;
    }

    /**************************************************************
     * URI host (pour la génération d'URL
     ***************************************************************/
    /** @var array */
    protected $consoleConfig;

    public function setConsoleConfig(array $consoleConfig)
    {
        $this->consoleConfig = $consoleConfig;
    }

    /**
     * Requis pour la génération des liens pour une action de console
     * @var RouteInterface
     */
    private $router;

    /**
     * @param RouteInterface $router
     */
    public function setRouter(RouteInterface $router)
    {
        $this->router = $router;
    }

    protected function useConsoleUri()
    {
        $this->isConsoleAction();
        if (!$this->consoleConfig) {
            throw new LogicException("La clé d'accès aux paramètres de configuration de la console 'console-cli' est introuvable.");
        }
        if (!isset($this->consoleConfig['uri-host'])) {
            throw new LogicException("La clé d'accès aux paramètres de configuration 'console-cli[uri-host]' est introuvable.");
        }
        if (!isset($this->consoleConfig['uri-scheme'])) {
            throw new LogicException("La clé d'accès aux paramètres de configuration 'console-cli[uri-scheme]' est introuvable.");
        }
        if (!$this->router) {
            throw new LogicException("L'interface de routage de la console n'as pas été définie");
        }
        $httpUri = (new Http())->setHost($this->consoleConfig['uri-host'])->setScheme($this->consoleConfig['uri-scheme']);
        $this->router->setRequestUri($httpUri);
    }
}
