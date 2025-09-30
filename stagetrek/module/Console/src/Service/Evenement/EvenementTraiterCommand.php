<?php

namespace Console\Service\Evenement;

use Application\Misc\Util;
use Application\Provider\Misc\EnvironnementProvider;
use DateInterval;
use DateTime;
use Evenement\Provider\EvenementEtatProvider;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenEvenement\Entity\Db\Etat;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenEvenement\Entity\Db\Journal;
use UnicaenEvenement\Service\Etat\EtatServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceAwareTrait;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionServiceAwareTrait;
use UnicaenEvenement\Service\Journal\JournalServiceAwareTrait;

class EvenementTraiterCommand extends Command
{
    protected static $defaultName = 'evenement:traiter';

    use EvenementGestionServiceAwareTrait;
    use EvenementServiceAwareTrait;
    use EtatServiceAwareTrait;
    use JournalServiceAwareTrait;

    protected function configure() : static
    {
        $this->setDescription("Traitements des événements");
        $this->addOption('evenement', '-e', InputOption::VALUE_OPTIONAL, "Id de l'événement");
        $this->addOption('force', '-f', InputOption::VALUE_OPTIONAL, "Force l'éxecution de l'événement s'il n'est pas en attente");
        return $this;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        try{
            $this->io = new SymfonyStyle($input, $output);
            $eventId = $input->getOption('evenement');

            if($eventId !== null){
                $eventId = intval($eventId, 0);
                if($eventId <=0) {
                    throw new Exception("L'identifiant de l'évenement n'est pas valide");
                }
                /** @var Evenement $event */
                $event = $this->getEvenementService()->find($eventId);
                if(!isset($event)){
                    throw new Exception("L'évenement d'identifiant ".$eventId." n'existe pas");
                }
                $force = boolval($input->getOption('force'));
                if($force !== null){$force = boolval($force);}
                if(!$force && $event->getEtat()->getCode() !== Etat::EN_ATTENTE ){
                    throw new Exception(sprintf("L'événement #%s n'est pas en attente", $eventId));
                }
                if(!$force && new DateTime() < $event->getDatePlanification()){
                    throw new Exception(sprintf("L'événement #%s est planifiée pour le %s", $eventId, Util::formattedDate($event->getDatePlanification(), "", 'd/m/Y H:i')));
                }

                if($force){
                    $enAttente = $this->getEtatEvenementService()->findByCode(Etat::EN_ATTENTE);
                    $event->setEtat($enAttente);
                }
                $this->evenements = [$event];
            }
            else{
                $this->evenements = $this->getEvenementService()->getEvenementsByEtat(Etat::EN_ATTENTE);
                $this->evenements = array_filter($this->evenements, function($e){
                    return new DateTime() > $e->getDatePlanification();
                });
            }
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            exit(-1);
        }
    }

    protected ?SymfonyStyle $io = null;
    protected array $evenements = [];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->title("Traitements des événements");
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
        if ($this->getJournalService()->isRunning()) {
            throw new Exception('Un traitement est déjà en cours !');
        }

        $evenements = $this->evenements;
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
        $annule = $this->getEtatEvenementService()->findByCode(Etat::ANNULE);
        $cpt = 0;
        /** @var Evenement $evenement */
        foreach ($evenements as $evenement) {
            try {
                $dateFinMax = DateTime::createFromFormat('d/m/Y H:i:s', $start->format('d/m/Y H:i:s'));
                $dateFinMax->add(new DateInterval($this->maxExecutionTime));
            } catch (Exception $e) {
                throw new RuntimeException("Impossible de calcul la date limite de traitement",0,$e);
            }

            // calcul du temps d'exécution
            if ($dateFinMax <= new DateTime()) {
                $this->io->writeln("");
                $this->io->warning("Arrêts du traitement des événements\n Temps d'execution trop long");
                // si le temps d'exécution maximal est dépassé on quitte la procédure
                break;
            }
            $nbOp++;
            try {
                if ($evenement !== null) {
                    $msg = sprintf("Début du traitement de l'événement #%s - %s", $evenement->getId(), $evenement->getType()->getLibelle());
                    $texte .= $msg . " <br/>";

                    if ($this->delaiPeremption !== null) {
                        try {
                            $dateLimiteTraitement = DateTime::createFromFormat('d/m/Y H:i:s', $evenement->getDatePlanification()->format('d/m/Y H:i:s'));
                            $dateLimiteTraitement->add(new DateInterval($this->delaiPeremption));
                        } catch (Exception $e) {
                            throw new RuntimeException("Impossible de calcul la date limite de traitement",0,$e);
                        }

                        if ($dateLimiteTraitement < new DateTime()) {
                            $evenement->setEtat($annule);
                            $evenement->setLog("Date Limite de Traitement dépassée, événement annulé");
                            $msg = sprintf("Annulation due traitement de l'événement #%s - %s : délai dépassé", $evenement->getId(), $evenement->getType()->getLibelle());
                            $this->io->writeln("");
                            $this->io->info($msg);
                            $this->evenementService->update($evenement);
                            break;
                        }
                    }

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

        $end = new DateTime();
        $texte .= "Fin du traitement : " . $end->format('d/m/Y H:i:s') . "";
        $this->io->text("Fin du traitement à ". $end->format('H:i:s'));
        $raport = "Rapport";
        $raport .= sprintf("\n * Nombre d'opérations : %s", $nbOp);
        if($nbError>0) {
            $raport .= sprintf("\n * Nombre d'opérations échouées : %s", $nbError);
        }
        $raport .= sprintf("\n Temps estimé : %s", $diff = $start->diff($end)->format('%Hh %Im %Ss'));

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

    /**
     * @var int $maxExecutionTime
     * @desc Temps d'exécution maximal (sec) du script de traitement des événements
     * !! celui-ci doit être en adéquation avec le cycle d'exécution du script de traitement des événements en attente dans la crontab du serveur
     * Cf cle de config ['unicaen-evenement']['max_time_execution']
     * vraleur par défaut : 300s
     * @file /etc/crontab
     */
    protected ?string $maxExecutionTime = null;
    public function getMaxExecutionTime(): string
    {
        return $this->maxExecutionTime;
    }
    public function setMaxExecutionTime(string $maxExecutionTime): void
    {
        $this->maxExecutionTime = $maxExecutionTime;
    }

    protected ?string $delaiPeremption = null;

    public function getDelaiPeremption(): ?string
    {
        return $this->delaiPeremption;
    }

    public function setDelaiPeremption(?string $delaiPeremption): void
    {
        $this->delaiPeremption = $delaiPeremption;
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