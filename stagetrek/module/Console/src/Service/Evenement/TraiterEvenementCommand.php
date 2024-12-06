<?php

namespace Console\Service\Evenement;

use Application\Provider\Misc\EnvironnementProvider;
use DateInterval;
use DateTime;
use Evenement\Provider\EvenementEtatProvider;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenEvenement\Entity\Db\Etat;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenEvenement\Entity\Db\Journal;
use UnicaenEvenement\Service\Etat\EtatServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceAwareTrait;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionServiceAwareTrait;
use UnicaenEvenement\Service\Journal\JournalServiceAwareTrait;

class TraiterEvenementCommand extends Command
{
    protected static $defaultName = 'traiter-evenements';

    use EvenementGestionServiceAwareTrait;
    use EvenementServiceAwareTrait;
    use EtatServiceAwareTrait;
    use JournalServiceAwareTrait;

    protected function configure() : static
    {
        $this->setDescription("Traitements des événements");
        return $this;
    }


    protected ?SymfonyStyle $io = null;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->title("Traitements des événements");
            $this->clearEventsEntry();
            $this->udpateErrorsEvents();
            $this->traiterEvenements();
            $this->io->success("Traitements des événements terminées");
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    public function traiterEvenements(): void
    {
        $this->io->title("Traitements des événements");
        if ($this->getJournalService()->isRunning()) {
            throw new Exception('Un traitement est déjà en cours !');
        }

        $evenements = $this->getEvenementService()->getEvenementsByEtat(Etat::EN_ATTENTE);
        if (empty($evenements)) {
            $this->io->text("Aucun événements à trairer");
            return;
        }
//        On trie les événements par date de planification pour faire en priorité les plus ancien
        usort($evenements, function (Evenement $e1, Evenement $e2) {
            return ($e1->getDatePlanification() < $e2->getDatePlanification()) ? -1 : 1;
        });

        $texte = "";
        $journal = new Journal();
        $journal->setDate(new DateTime());
        $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::EN_COURS));
        $this->getJournalService()->create($journal);
        $start = new DateTime();
        $this->io->text("Debut du traitement à ". $start->format('H:i:s'));
        $texte .= "Traitement des événements en attente <br/>";
        $texte .= "Début du traitement : " . (new DateTime())->format('d/m/Y H:i:s') . "<br/>";
        $journal->setLog($texte);
        $this->getJournalService()->update($journal);
        $nbOp = 0;
        $nbError = 0;

        $this->io->progressStart(sizeof($evenements));
        $cpt = 0;
        /** @var Evenement $evenement */
        foreach ($evenements as $evenement) {
            // calcul du temps d'exécution
            $executionTime = (new DateTime())->getTimestamp() - $start->getTimestamp();
            if ($executionTime >= $this->maxExecutionTime) {
                $this->io->writeln("");
                $this->io->warning("Arrêts du traitement des événements\n Temps d'execution > " . $this->maxExecutionTime . "s");
                // si le temps d'exécution maximal est dépassé on quitte la procédure
                break;
            }
            $nbOp++;
            try {
                if ($evenement !== null) {
                    $msg = sprintf("Début du traitement de l'événement #%s - %s", $evenement->getId(), $evenement->getType()->getLibelle());
                    $texte .= $msg . " <br/>";
                    if(!$this->hasToSimultateTraitement()){
                        $evenement = $this->evenementGestionService->traiterEvent($evenement);
                    }
                    else{
                        $evenement = $this->simulateTraitement($evenement);
                    }
                    if ($evenement->getEtat()->getCode() == Etat::SUCCES) {
                        $msg = sprintf("- Evenement #%s - %s", $evenement->getId(), $evenement->getEtat()->getCode());
                        $texte .= $msg . " <br/>";
                    } else {
                        $msg = sprintf("Echec de traitement de l'événement #%s - %s", $evenement->getId(), $evenement->getType()->getLibelle());
                        $this->io->writeln("");
                        $this->io->warning($msg);
                        $texte .= $msg . " <br/>";
                        $nbError++;
                        if($nbError >= 10){
                            $this->io->writeln("");
                            $this->io->error("Arrêts du traitement des événements\n Nombre d'erreur > 10");
                            // si le temps d'exécution maximal est dépassé on quitte la procédure
                            break;
                        }
                    }
                }
                $this->io->progressAdvance();
            } catch (Exception $e) {
                try{
                    $nbOp++;
                    $nbError++;
                    $this->io->writeln("");
                    $this->io->error($e->getMessage());
                    $texte .= "<strong> Échec du traitement </strong> <br/>";
                    $failure = $this->getEtatEvenementService()->findByCode(Etat::ECHEC);
                    $evenement->setEtat($failure);
                    $this->getEvenementService()->update($evenement);
                    if($nbError >= 10){
                        $this->io->writeln("");
                        $this->io->error("Arrêts du traitement des événements\n Nombre d'erreur > 10");
                        // si le temps d'exécution maximal est dépassé on quitte la procédure
                        break;
                    }
                }
                catch(Exception $e){break;};

            }
        }
        if($nbOp == sizeof($evenements)){
            $this->io->progressFinish();
        }

        $texte .= "Fin du traitement : " . (new DateTime())->format('d/m/Y H:i:s') . "";
        $this->io->text("Fin du traitement à ". $start->format('H:i:s'));
        $raport = "Rapport";
        $raport .= sprintf("\n * Nombre d'opérations : %s", $nbOp);
        if($nbError>0) {
            $raport .= sprintf("\n * Nombre d'opérations échouées : %s", $nbError);
        }
        $raport .= sprintf("\n Temps estimé : %s", $diff = $start->diff(new DateTime())->format('%Hh %Im %Ss'));

        $this->io->info($raport);
        $journal->setLog($texte);
        if($nbError==0){
            $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::SUCCES));
        }
        else{
            $journal->setEtat($this->getEtatEvenementService()->findByCode(Etat::ECHEC));
        }
        $this->getJournalService()->update($journal);
    }


//    En jours
    const CONSERVATION_LOG_TIME = 30;

    private function clearEventsEntry() : void
    {
        $this->io->section("Suppressions des anciens événements");
        $dateSuppression = new DateTime();
        $dateSuppression->sub(new DateInterval('P' . self::CONSERVATION_LOG_TIME . 'D'));
        $dateSuppression->setTime(0, 0);
        $evenements = $this->getEvenementService()->getEvenements();
        $evenements = array_filter($evenements, function ($evenement) use ($dateSuppression) {
            $dateTraitement = $evenement->getDateTraitement();
            if (isset($dateTraitement) && $dateTraitement < $dateSuppression) {
                return true;
            }
            $datePlanigication = $evenement->getDatePlanification();
            if (isset($datePlanigication) && $datePlanigication < $dateSuppression) {
                return true;
            }
            return false;
        });
        if(!empty($evenements)) {
            $this->io->progressStart(sizeof($evenements));
            /** @var Evenement $evenement */
            foreach ($evenements as $evenement) {
                $this->getEvenementService()->delete($evenement);
                $this->io->progressAdvance();
            }
            $this->io->progressFinish();
        }
        else{
            $this->io->text("Aucun événement à supprimer");
        }

        $this->io->section("Suppressions des anciens logs");
        $logs = $this->getJournalService()->getJounaux();
        $logs = array_filter($logs, function (Journal $log) use ($dateSuppression) {
            return  ($log->getDate() < $dateSuppression);
        });

        if(!empty($logs)) {
            $this->io->progressStart(sizeof($logs));
            /** @var Journal $log */
            foreach ($logs as $log) {
                $this->getJournalService()->delete($log);
                $this->io->progressAdvance();
            }
            $this->io->progressFinish();
        }
        else{
            $this->io->text("Aucun log à supprimer");
        }
    }

    private function udpateErrorsEvents() : void
    {
        $this->io->section("Mise à jours des états");
        $evenements = $this->getEvenementService()->getEvenements();
        if(sizeof($evenements) == 0){
            $this->io->text("Aucun événements à mettre à jours");
            return;
        }
        $this->io->progressStart(sizeof($evenements));
        /** @var Evenement $evenement */
        foreach ($evenements as $evenement) {
            //Mise en echec d'un événement en cours de traitement trop vieux
            if ($evenement->getEtat()->getCode() == Etat::EN_COURS) {
                // calcul du temps d'exécution
                $time = new DateTime();
                $executionTime = $time->getTimestamp() - $evenement->getDatePlanification()->getTimestamp();
                $delayBeforeError = 2 * $this->getMaxExecutionTime();
                if ($executionTime >= $delayBeforeError) {
                    $this->io->writeln("");
                    $this->io->warning("Mise en échec de l'événement #" . $evenement->getId());
                    $log = $evenement->getLog();
                    $log .= sprintf("<br/>-----------<br/>");
                    $log .= sprintf("Mise en echec le %s à %s car temps d'exécution trop long <br/>", $time->format('d/m/Y'), $time->format('H:i'));
                    $log .= sprintf("-----------<br/>");
                    $evenement->setLog($log);
                    $evenement->setEtat($this->getEtatEvenementService()->findByCode(Etat::ECHEC));
                    $evenement->setDateFin($time);
                    $this->getEvenementService()->update($evenement);
                }
            }

            //Mise en echec d'un événement en cours de traitement trop vieux
            if ($evenement->getEtat()->getCode() == Etat::EN_ATTENTE) {
                // calcul du temps d'exécution
                $time = new DateTime();
                $executionTime = $time->getTimestamp() - $evenement->getDatePlanification()->getTimestamp();
                $delayBeforeError = 60 * 60 * 24 ; // Un non traité en attente depuis plus de 24h sera annulée
                if ($executionTime >= $delayBeforeError) {
                    $this->io->writeln("");
                    $this->io->warning("Annulation de de l'événement #" . $evenement->getId());
                    $log = $evenement->getLog();
                    $log .= sprintf("<br/>-----------<br/>");
                    $log .= sprintf("Annulée le %s à %s car non traiter à temps <br/>", $time->format('d/m/Y'), $time->format('H:i'));
                    $log .= sprintf("-----------<br/>");
                    $evenement->setLog($log);
                    $evenement->setEtat($this->getEtatEvenementService()->findByCode(Etat::ANNULE));
                    $evenement->setDateFin($time);
                    $this->getEvenementService()->update($evenement);
                }
            }
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();
    }

    /**
     * @var int $maxExecutionTime
     * @desc Temps d'exécution maximal (sec) du script de traitement des événements
     * !! celui-ci doit être en adéquation avec le cycle d'exécution du script de traitement des événements en attente dans la crontab du serveur
     * Cf cle de config ['unicaen-evenement']['max_time_execution']
     * vraleur par défaut : 300s
     * @file /etc/crontab
     */
    protected int $maxExecutionTime = 300;
    public function getMaxExecutionTime(): int
    {
        return $this->maxExecutionTime;
    }
    public function setMaxExecutionTime(int $maxExecutionTime): void
    {
        $this->maxExecutionTime = $maxExecutionTime;
    }

    /** @var string|null $environnement */
    protected ?string $environnement = null;

    public function getEnvironnement(): ?string
    {
        return $this->environnement;
    }

    public function setEnvironnement(string $environnement): void
    {
        $this->environnement = $environnement;
    }

    public function hasToSimultateTraitement(): bool
    {
//        return false;
        return $this->environnement != EnvironnementProvider::PRODUCTION;
    }
    public function simulateTraitement(Evenement $evenement): Evenement
    {
        $success = $this->getEtatEvenementService()->findByCode(EvenementEtatProvider::SUCCES);
        $evenement->setEtat($success);
        $evenement->setLog(sprintf("Simultation du traitement de l'événement car environnement %s", $this->getEnvironnement()));
        $evenement->setDateTraitement(new DateTime());
        $this->evenementService->update($evenement);
        return $evenement;
    }
}