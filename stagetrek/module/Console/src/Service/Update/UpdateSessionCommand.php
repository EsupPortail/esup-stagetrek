<?php

namespace Console\Service\Update;

use Application\Entity\Db\SessionStage;
use Application\Service\Stage\SessionStageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSessionCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update:sessions';

    /**
     * @throws \Exception
     */
    protected function configure(): static
    {
        $this->setDescription("Mise à jours automatiques des sessions de stages");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(SessionStageService::class);
        $io = $this->initInputOut($input, $output);
        try {
            $io->title("Maj des sessions de stages");
            $this->updateEtat();
            $this->updatePlaces();
            $this->updateDemandes();
            $io->success("Maj terminée");

        } catch (Exception $e) {
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
        /** @var SessionStageService $service */
        $service = $this->getEntityService();
        $sessions = $service->findAll();
        $io->progressStart(sizeof($sessions));
        /** @var SessionStage $session */
        foreach ($sessions as $session) {
            $etat = $session->getEtatActif();
            $service->updateEtat($session);
            $newEtat = $session->getEtatActif();
            if (!isset($etat) || $newEtat->getId() != $etat->getId()) {
                $annee = $session->getAnneeUniversitaire();
                $msg = sprintf("Année %s | session n°%s : %s", $annee->getLibelle(), $session->getLibelle(), $newEtat->getType()->getLibelle());
                $infos = $newEtat->getInfos();
                if ($infos !== null && $infos !== "") {
                    $msg .= PHP_EOL . $infos;
                }
                $this->addLog($msg);
            }
            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }

    /**
     * @throws Exception
     */
    protected function updatePlaces() : void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Places disponibles");
        $io->progressStart(1);
        /** @var SessionStageService $service */
        $service = $this->getEntityService();
        $service->computePlacesForSessions();
        $io->progressAdvance();
        $io->progressFinish();
        $this->renderLog();
    }

    /**
     * @throws Exception
     */
    protected function updateDemandes() : void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Niveaux de demandes des terrains de stages");
        /** @var SessionStageService $service */
        $service = $this->getEntityService();
        $sessions = $service->findAll();
        $io->progressStart(sizeof($sessions));
        foreach ($sessions as $session) {
            $service->recomputeDemandeTerrains($session);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }

}