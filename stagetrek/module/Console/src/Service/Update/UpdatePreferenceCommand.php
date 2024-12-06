<?php

namespace Console\Service\Update;

use Application\Entity\Db\Stage;
use Application\Service\Preference\PreferenceService;
use Application\Service\Stage\StageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePreferenceCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update-preferences';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des préférences");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(PreferenceService::class);
        $io = $this->initInputOut($input, $output);
        $io->title("Maj des préférences");
        $io->section("Satisfactions des préférences");
        try{
            /** @var PreferenceService $preferenceService */
            $preferenceService = $this->getEntityService();
            /** @var StageService $stageService */
            $stageService = $this->getServiceManager()->get(StageService::class);
            $stages = $stageService->findAll();
            $io->progressStart(sizeof($stages));
            /** @var Stage $stage */
            foreach ($stages as $stage) {
                $preferenceService->updatePreferenceSatisfaction($stage);
                $io->progressAdvance();
            }
            $io->progressFinish();
            $this->renderLog();
            $io->success("Maj terminée");

        }catch (Exception $e){
            $io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

}