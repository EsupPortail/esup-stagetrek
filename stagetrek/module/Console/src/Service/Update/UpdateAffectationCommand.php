<?php

namespace Console\Service\Update;

use Application\Entity\Db\AffectationStage;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Stage\StageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAffectationCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update-annees';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des affectations de stages");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(AffectationStageService::class);
        $io = $this->initInputOut($input, $output);
        $io->title("Maj des affectations");
        try{


            $this->updatePreferenceSat();
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
    protected function getStageService() : StageService
    {
        try {
            return $this->getServiceManager()->get(StageService::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function updateEtat() : void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Etats");
        /** @var AffectationStageService $service */
        $service = $this->getEntityService();
        $affectations = $service->findAll();
        $io->progressStart(sizeof($affectations));

        /** @var AffectationStage $affectation */
        foreach ($affectations as $affectation) {
            $etat = $affectation->getEtatActif();
            $service->updateEtat($affectation);
            $newEtat = $affectation->getEtatActif();
            if(!isset($etat) || $newEtat->getId() != $etat->getId()){
                $stage = $affectation->getStage();
                $etudiant = $stage->getEtudiant();
                $msg = sprintf("Affectation du stage n°%s de %s : %s", $stage->getNumero(), $etudiant->getNumEtu(), $newEtat->getType()->getLibelle());
                $infos = $newEtat->getInfos();
                if($infos !== null && $infos !== ""){
                    $msg .= PHP_EOL . $infos;
                }
                //Changement possible de l'état du stage
                //Choix fait pour éviter les boucles de mettre a jours l'état du stage mais de ne pas poursuive en remettant a jours l'état de l'affectation ...
                $this->getStageService()->updateEtat($stage);
                $this->addLog($msg);
            }

            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }

    private function updatePreferenceSat() : void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Recalcul des préférences satisfaites");
        /** @var AffectationStageService $service */
        $service = $this->getEntityService();
        $affectations = $service->findAll();
        $io->progressStart(sizeof($affectations));

        /** @var AffectationStage $affectation */
        foreach ($affectations as $affectation) {
            $service->updatePreferenceSatisfaction($affectation);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }
}