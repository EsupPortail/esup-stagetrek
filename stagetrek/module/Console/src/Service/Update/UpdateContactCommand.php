<?php

namespace Console\Service\Update;

use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Service\Affectation\AffectationStageService;
use Application\Service\Contact\ContactStageService;
use Application\Service\Etudiant\EtudiantService;
use Application\Service\Stage\StageService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use DateTime;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateContactCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update-contacts';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des contacts des stages");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(ContactStageService::class);
        $io = $this->initInputOut($input, $output);
        try{
            $io->title("Maj des contacts");
            $this->updateListe();
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
    protected function updateListe(): void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Création des contacts de stages");
        $io->progressStart(100);
        /* @var ContactStageService $service */
        $service = $this->getEntityService();
        $io->progressAdvance(25);
        $service->updateContactsStage();
        $io->progressAdvance(75);
        $io->progressFinish();
        $this->renderLog();
    }

}