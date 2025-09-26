<?php

namespace Console\Service\Update;

use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Misc\Util;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Stage\StageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use DateTime;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenEvenement\Entity\Db\Etat;

class UpdateOrdreAffectationCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update:ordres-affectations';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Calcul automatiques des ordres d'affectation des stages");
        $this->addOption('session', '-s', InputOption::VALUE_OPTIONAL, "Id de la session de stage");
        $this->addOption('force', '-f', InputOption::VALUE_NONE, "Forcer le caclul pour la session de stage si l'on est en dehors des dates");
        return $this;
    }

    protected array $sessions = [];
    public function getSessionsStages() : array
    {
        return $this->sessions;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->io = new SymfonyStyle($input, $output);
            $sessionId = $input->getOption('session');
            $force = $input->getOption('force');
            if($sessionId !== null) {
                $sessionId = intval($sessionId, 0);
                if($sessionId <=0) {
                    throw new Exception("L'identifiant de la session n'est pas valide");
                }
                $session = $this->getObjectManager()->getRepository(SessionStage::class)->find($sessionId);
                if(!isset($session)){
                    throw new Exception("La session d'identifiant ".$sessionId." n'existe pas");
                }
                $this->sessions[] = $session;
            }
            else{
                if($force){
                    throw new Exception("L'option --force requiére de spécifier la session de stage");
                }
                $this->sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
            }
            if(!$force){
                $today = new DateTime();
                $this->sessions = array_filter($this->sessions, function (SessionStage $session) use ($today) {
                    if($today < $session->getDateCalculOrdresAffectations()){return false;}
                    if($session->getDateDebutChoix() < $today){return false;}
        //          on ne lance le calcul que s'il existe un stage n'ayant pas d'ordre définit. Sinon, on se basera sur une modification manuel de l'ordre d'affectation
                    $stage = $session->getStages();
                    /** @var Stage $s */
                    foreach ($stage as $s){
                        if($s->getOrdreAffectationAutomatique() == null){
                            return true;
                        }
                    }
                    return false;
                });
            }
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            exit(-1);
        }
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(StageService::class);
        $io = $this->initInputOut($input, $output);
        try{
            $io->title("Maj des ordres d'affectations");
            $this->updateOrdresAffectations();
            $io->success("Maj terminée");

        }catch (Exception $e){
            $io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    /**
     * @throws Exception
     */
    protected function updateOrdresAffectations() : void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Ordres d'affectations");
        $sessions = $this->getSessionsStages();
        if(sizeof($sessions) == 0) {
            $io->info("Pas de calcul d'ordres d'affectations automatiques requis");
            $this->renderLog();
            return;
        }
        $io->progressStart(sizeof($sessions));
        /** @var StageService $service */
        $service = $this->getEntityService();
        foreach ($sessions as $session){
            $service->updateOrdresAffectationsAuto($session);
            $service->updateOrdresAffectations($session);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }

}