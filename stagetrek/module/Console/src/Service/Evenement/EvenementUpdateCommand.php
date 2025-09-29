<?php

namespace Console\Service\Evenement;

use DateTime;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenEvenement\Entity\Db\Etat;
use UnicaenEvenement\Entity\Db\Journal;
use UnicaenEvenement\Service\Etat\EtatServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceAwareTrait;
use UnicaenEvenement\Service\Journal\JournalServiceAwareTrait;

class EvenementUpdateCommand extends Command
{
    protected static $defaultName = 'evenement:update';

    use EvenementServiceAwareTrait;
    use EtatServiceAwareTrait;
    use JournalServiceAwareTrait;
    protected int $maxExecutionTime = 300;

    /**
     * @desc met en état Annulée les événements trop ancien
     */
    protected function configure(): static
    {
        $this->setDescription("Mise à jours des événements");
        return $this;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->io->title("Mise à jours des événements");
            $this->updateEvenementsEtat();
            $this->io->success("Mise à jours des événements terminée");
            $res = self::SUCCESS;
        } catch (Exception $e) {
            $this->io->error($e->getMessage());
            $res = self::FAILURE;
        }
        return ($res) ?? self::FAILURE;
    }

    protected ?SymfonyStyle $io = null;

//    Création automatique des évenements prévu pour la journée

    private function updateEvenementsEtat(): void
    {
        $failureEtat = $this->getEtatEvenementService()->findByCode(Etat::ECHEC);
        $cancelEtat = $this->getEtatEvenementService()->findByCode(Etat::ANNULE);
        $successEtat = $this->getEtatEvenementService()->findByCode(Etat::SUCCES);
        $this->io->section("Mise à jours des états");
        $evenements = $this->getEvenementService()->getEvenements();
        if (sizeof($evenements) == 0) {
            $this->io->info("Aucun événements à mettre à jours");
            return;
        }
        $this->io->progressStart(sizeof($evenements));
        $cptCancel = 0;
        $cptError = 0;
        foreach ($evenements as $evenement) {
            //Mise en echec d'un événement en cours de traitement "trop vieux" (soit ayant atteint le temps maximum d'execution
            if ($evenement->getEtat()->getCode() == Etat::EN_COURS) {
                // calcul du temps d'exécution
                $time = new DateTime();
                $executionTime = $time->getTimestamp() - $evenement->getDatePlanification()->getTimestamp();
                $delayBeforeError = $this->getMaxExecutionTime();
                if ($executionTime >= $delayBeforeError) {
                    $this->io->warning("Mise en échec de l'événement #" . $evenement->getId());
                    $log = $evenement->getLog();
                    $log .= "<br/>-----------<br/>";
                    $log .= sprintf("Mise en echec le %s à %s car temps d'exécution trop long <br/>", $time->format('d/m/Y'), $time->format('H:i'));
                    $log .= "-----------<br/>";
                    $evenement->setLog($log);
                    $evenement->setEtat($failureEtat);
                    $evenement->setDateFin($time);
                    $this->getEvenementService()->update($evenement);
                    $cptError++;
                }
            }
            //Annulation d'événement qui aurais du petre traiter la veille
            if ($evenement->getEtat()->getCode() == Etat::EN_ATTENTE) {
                // calcul du temps d'exécution
                $time = new DateTime();
                $executionTime = $time->getTimestamp() - $evenement->getDatePlanification()->getTimestamp();
                $delayBeforeError = 60 * 60 * 24; // Un non traité en attente depuis plus de 24h sera annulée
                if ($executionTime >= $delayBeforeError) {
                    $this->io->warning("Annulation de de l'événement #" . $evenement->getId());
                    $log = $evenement->getLog();
                    $log .= "<br/>-----------<br/>";
                    $log .= sprintf("Annulée le %s à %s car non traiter à temps <br/>", $time->format('d/m/Y'), $time->format('H:i'));
                    $log .= "-----------<br/>";
                    $evenement->setLog($log);
                    $evenement->setEtat($cancelEtat);
                    $evenement->setDateFin($time);
                    $this->getEvenementService()->update($evenement);
                    $cptCancel++;
                }
            }
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();

        if($cptError+$cptCancel>0){
            $journal = new Journal();
            $journal->setDate(new DateTime());
            $journalLog = "Mise à jours des états<br/>";
            $journalLog .= "-----------<br/>";
            if($cptError>0){
                $journalLog .= sprintf("%s événements mis en erreur<br/>", $cptError);
            }
            if($cptCancel>0){
                $journalLog .= sprintf("%s événements annulés<br/>", $cptCancel);
            }
            $journal->setLog($journalLog);
            $journal->setEtat($successEtat);
            $this->getJournalService()->create($journal);
        }
    }

    public function getMaxExecutionTime(): int
    {
        return $this->maxExecutionTime;
    }

    public function setMaxExecutionTime(int $maxExecutionTime): static
    {
        $this->maxExecutionTime = $maxExecutionTime;
        return $this;
    }
}