<?php

namespace Console\Service\Update;

use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\ValidationStage;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Stage\StageService;
use Application\Service\Stage\ValidationStageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use DateTime;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateValidationStageCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update:validations-stages';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des validations de stage");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(ValidationStageService::class);
        $io = $this->initInputOut($input, $output);
        try{
            $io->title("Maj des validations des stages");
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
        /** @var ValidationStageService $service */
        $service = $this->getEntityService();
        $validations = $service->findAll();
        $io->progressStart(sizeof($validations));
        /** @var ValidationStage $validation */
        foreach ($validations as $validation) {
            $etat = $validation->getEtatActif();
            $service->updateEtat($validation);
            $newEtat = $validation->getEtatActif();
            if(!isset($etat) || $newEtat->getId() != $etat->getId()){
                $stage = $validation->getStage();
                $etudiant = $stage->getEtudiant();
                $msg = sprintf("Etudiant %s | stage n°%s : %s", $etudiant->getNumEtu(), $stage->getNumero(true), $newEtat->getType()->getLibelle());
                $infos = $newEtat->getInfos();
                if($infos !== null && $infos !== ""){
                    $msg .= PHP_EOL . $infos;
                }
                $this->addLog($msg);
            }

            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }


}