<?php

namespace Console\Service\Update;

use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Stage\StageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use DateTime;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStageCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update-stages';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des stages");
        return $this;
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
            $io->title("Maj des stages");
            $this->updateListesStages();
            $this->updateEtat();
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
    protected function updateEtat(): void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Etats");
        /** @var StageService $service */
        $service = $this->getEntityService();
        $stages = $service->findAll();
        $io->progressStart(sizeof($stages));
        /** @var Stage $stage */
        foreach ($stages as $stage) {
            $etat = $stage->getEtatActif();
            $service->updateEtat($stage);
            $newEtat = $stage->getEtatActif();
            if(!isset($etat) || $newEtat->getId() != $etat->getId()){
                $etudiant = $stage->getEtudiant();
                $msg = sprintf("Etudiant %s | stage n°%s : %s", $etudiant->getNumEtu(), $stage->getNumero(true), $newEtat->getType()->getLibelle());
                $infos = $newEtat->getInfos();
                if($infos !== null && $infos !== ""){
                    $msg .= PHP_EOL . $infos;
                }
                $this->addLog($msg);
                //Un changement d'état du stage peux proboquer la maj de l'état de l'affectation
                $affectation = $stage->getAffectationStage();
                $this->getAffectationStageService()->updateEtat($affectation);
            }

            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }

//    TODO : a splitter

    /**
     * @throws Exception
     */
    private function updateListesStages() : void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Listes des stages");
        $io->progressStart(1);
        /** @var StageService $service */
        $service = $this->getEntityService();
        $service->updateStages();
        $io->progressAdvance();
        $io->progressFinish();
        $this->renderLog();
    }

    /**
     * @throws Exception
     */
    protected function getAffectationStageService() : AffectationStageService
    {
        try {
            return $this->getServiceManager()->get(AffectationStageService::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
    }

}