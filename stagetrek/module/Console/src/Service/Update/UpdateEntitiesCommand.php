<?php

namespace Console\Service\Update;

use Application\Entity\Db\Parametre;
use Application\Service\AnneeUniversitaire\Traits\AnneeUniversitaireServiceAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Contrainte\Traits\ContrainteCursusServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenEtat\Entity\Db\EtatInstance;
use UnicaenEvenement\Entity\Db\Evenement;
use UnicaenEvenement\Entity\Db\Journal;
use UnicaenMail\Entity\Db\Mail;

class UpdateEntitiesCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update-entities';

    protected function configure() : static
    {
        $this
//            ->addArgument('source', InputArgument::REQUIRED, 'Base de données source')
//            ->addArgument('desination', InputArgument::REQUIRED, 'Base de données de destination')
            ->setDescription("Mise à jours automatiques des données");
        return $this;
    }

    protected ?SymfonyStyle $io = null;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title("Maj des données");

        try{
            $this->updateContrainteCursus();
            $this->updateAnnees();

            $this->updateSessions();

            $this->updateStages();

            $this->updateEtudiants();

            $this->clearMails();
            $this->clearEventsLogs();

            $this->io->success("Maj terminée");
        }catch (Exception $e){
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    use EtudiantServiceAwareTrait;
    use ContrainteCursusServiceAwareTrait;
    use ContrainteCursusEtudiantServiceAwareTrait;

    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    protected function updateContrainteCursus() : void
    {
        $this->io->text("Mises à jours des contraintes de cursus");
        $this->io->text("----");
        $this->execProcedure('update_contraintes_cursus');
        $this->io->text("Mise à jours des états des contraintes de cursus des étudiants");
        $this->io->text("----");
        $contraintesCursusEtudiant = $this->getContrainteCursusEtudiantService()->findAll();
        $this->getContrainteCursusEtudiantService()->updateEtats($contraintesCursusEtudiant);
    }

    use AnneeUniversitaireServiceAwareTrait;

    /**
     * @throws \Exception
     */
    private function updateAnnees(): void
    {
        $this->io->section("Mise à jours des années universitaires");
        $annees = $this->getAnneeUniversitaireService()->findAll();
        $this->getAnneeUniversitaireService()->updateEtats($annees);
    }

    use SessionStageServiceAwareTrait;
    /**
     * @throws \Exception
     */
    private function updateSessions(): void
    {
        $this->io->section("Mise à jours des sessions de stages");
        $sessions = $this->getSessionStageService()->findAll();
        $this->getSessionStageService()->updateEtats($sessions);
        //TODO : lancer le calcul des ordres d'affectations si necessaires

    }

    use StageServiceAwareTrait;
    /**
     * @throws \Exception
     */
    private function updateStages(): void
    {
        $this->io->section("Mise à jours des stages");
        $stages = $this->getStageService()->findAll();
        $this->getStageService()->updateEtats($stages);
        //TODO : lancer le calcul des ordres d'affectations si necessaires

    }


    use AnneeUniversitaireServiceAwareTrait;
    /**
     * @throws \Exception
     */
    private function updateEtudiants(): void
    {
        $this->io->section("Mise à jours des étudiants");
        $etudiants = $this->getEtudiantService()->findAll();
    }

    //Nettoyage des etat instance
    use ParametreServiceAwareTrait;
    protected function clearMails() : void
    {
        $this->io->section("Nettoyages des mails");
        $delay = $this->getParametreService()->getParametreValue(Parametre::CONSERVATION_MAIL);
        $dateSup = new DateTime();
        $dateSup->sub(new DateInterval('P' . $delay . 'D'));
        $mails = $this->getObjectManager()->getRepository(Mail::class)->findAll();
        $mails = array_filter($mails, function (Mail $m) use ($dateSup) {
            if(!$m->getDateEnvoi() === null){return false;}
            return $m->getDateEnvoi() < $dateSup;
        });
        foreach ($mails as $m){
            $this->getObjectManager()->remove($m);
        }
        $this->getObjectManager()->flush();
    }

    protected function clearEtatsLogs() : void
    {
        $this->io->section("Nettoyages des logs des états");
        $delay = $this->getParametreService()->getParametreValue(Parametre::CONSERVATION_LOG);
        $dateSup = new DateTime();
        $dateSup->sub(new DateInterval('P' . $delay . 'D'));
        $etatInstances = $this->getObjectManager()->getRepository(EtatInstance::class)->findAll();
        $etatInstances = array_filter($etatInstances, function (EtatInstance $e) use ($dateSup) {
            if($e->estNonHistorise()){return false;}
            return $e->getHistoDestruction() < $dateSup;
        });
        foreach ($etatInstances as $etatInstance){
            $this->getObjectManager()->remove($etatInstance);
        }
        $this->getObjectManager()->flush();
    }

    protected function clearEventsLogs() : void
    {
        $this->io->section("Nettoyages des logs des événements");
        $delay = $this->getParametreService()->getParametreValue(Parametre::CONSERVATION_EVENEMENT);
        $dateSup = new DateTime();
        $dateSup->sub(new DateInterval('P' . $delay . 'D'));
        $events = $this->getObjectManager()->getRepository(Evenement::class)->findAll();
        $events = array_filter($events, function (Evenement $e) use ($dateSup) {
            if($e->getDateTraitement() === null){return false;}
            return $e->getDateTraitement() < $dateSup;
        });
        foreach ($events as $event){
            $this->getObjectManager()->remove($event);
        }

        $journaux = $this->getObjectManager()->getRepository(Journal::class)->findAll();
        $journaux = array_filter($journaux, function (Journal $j) use ($dateSup) {
            if($j->getDate() === null){return true;}
            return $j->getDate() < $dateSup;
        });
        foreach ($journaux as $j){
            $this->getObjectManager()->remove($j);
        }
        $this->getObjectManager()->flush();
    }

}